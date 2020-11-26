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

        if ($this->checkIfAlreadyExists())
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

    public static function removeFromDB(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM favorites WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
            logger("Remove Favorite failed " . $stmt->error);
        else
            logger("Removed Favorite successfully!");

        $stmt->close();

        CloseCon($conn);
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

    public function checkIfAlreadyExists():bool
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT ID FROM favorites WHERE USERID=? AND MOVIEID=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("ss",$user_id, $movie_id);
        $user_id = $this->user_id;
        $movie_id = $this->movie_id;

        if (!$stmt->execute())
            logger("Check for duplicate favorite failed " . $stmt->error . ".");

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
