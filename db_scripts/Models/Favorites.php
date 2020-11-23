
<?php

class Favorite
{
    public string $id;
    public string $user_id;
    public string $movie_id;

    const FAVES_ID_PREFIX = "f";

    public function __construct($user_id, $movie_id)
    {
        $this->generateID();
        $this->user_id = $user_id;
        $this->movie_id = $movie_id;
    }

    public function addToDB():bool
    {

    }

    public static function removeFromDB(string $id)
    {

    }

    private function generateID()
    {
        do {
            $this->id = getRandomString(9, Favorite::FAVES_ID_PREFIX);
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
            echo "Check Favorites ID failed " . $stmt->error . "\n";

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
