<?php

class Favorite
{
    public string $id;
    public string $user_id;
    public string $movie_id;

    const ID_PREFIX = "f";

    public function __construct($user_id, $movie_id)
    {
        $this->generateID();
        $this->user_id = $user_id;
        $this->movie_id = $movie_id;
    }

    public function addToDB():bool
    {
        if (empty($this->id))
        {
            logger("ID was empty.");
            return false;
        }
        if (empty($this->user_id))
        {
            logger("User ID was empty.");
            return false;
        }
        if (empty($this->movie_id))
        {
            logger("Movie ID was empty.");
            return false;
        }

        if ($this->IsFavorite($this->user_id, $this->movie_id))
            logger("Favorite already exists!");

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO favorites VALUES(?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sss", $id,$user_id, $movie_id))
            logger("Binding error while Adding Favorites.");

        $id = $this->id;
        $user_id = $this->user_id;
        $movie_id = $this->movie_id;

        if (!$stmt->execute())
        {
            logger("Add Favorite failed: " . $stmt->error . ".");
            $stmt->close();
            CloseCon($conn);
            return false;
        }
        else
        {
            logger("Added Favorite successfully..");
            $stmt->close();
            CloseCon($conn);
            return true;
        }
    }



    private function generateID()
    {
        do {
            $this->id = getRandomString(9, $this::ID_PREFIX);
        } while($this->checkIfUniqueID() === false);
    }

    public function checkIfUniqueID():bool
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT ID FROM favorites WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);
        $id = $this->id;

        if (!$stmt->execute())
            logger("Check Favorites ID failed " . $stmt->error . ".");

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

    public static function AddFavorite($user_id, $movie_id):bool
    {
        $favorite = new Favorite($user_id, $movie_id);
        return $favorite->addToDB();
    }

    public static function DeleteFavorite(string $id):bool
    {
        if (empty($id))
            return false;

        $conn = OpenCon(true);

        $sql_str = "DELETE FROM favorites WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
        {
            logger("Remove Favorite failed " . $stmt->error);
            $success =  false;
        }
        else
        {
            logger("Removed Favorite successfully!");
            $success =  true;
        }

        $stmt->close();
        CloseCon($conn);

        return $success;
    }

    public static function ToggleFavorite($user_id, $movie_id, $setFavorite):bool
    {
        if(empty($setFavorite))
        {
            $id = self::IsFavorite($user_id, $movie_id);
            if (empty($id))
                return self::AddFavorite($user_id, $movie_id);
            else
                return self::DeleteFavorite($id);
        }
        else if ($setFavorite === "true")
            return self::AddFavorite($user_id, $movie_id);
        else
            return self::DeleteFavorite(self::IsFavorite($user_id, $movie_id));

    }

    public static function IsFavorite($user_id, $movie_id):string
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT ID FROM favorites WHERE USERID=? AND MOVIEID=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("ss",$user_id, $movie_id);

        if (!$stmt->execute())
            logger("Check for favorite failed " . $stmt->error . ".");

        $result = $stmt->get_result();

        if ($result->num_rows === 1)
            return ($result->fetch_assoc())['ID'];
        else
            return "";
    }


}
