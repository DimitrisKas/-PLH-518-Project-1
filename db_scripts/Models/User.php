
<?php

class User
{
    public string $id;
    public string $name;
    public string $surname;
    public string $username;
    public string $password;
    public string $email;
    public string $role;
    public bool $confirmed;

    const ADMIN = "ADMIN";
    const CINEMAOWNER = "CINEMAOWNER";
    const USER = "USER";

    const ID_PREFIX = "u";

    public function __construct($name, $surname, $username, $password, $email, $role, $confirmed)
    {
        $this->generateID();
        $this->name = $name;
        $this->surname = $surname;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->confirmed = $confirmed;
    }

    public function addToDB():bool
    {
        if (empty($this->id))
        {
            logger("ID was empty.");
            return false;
        }
        if (empty($this->username))
        {
            logger("Username was empty.");
            return false;
        }
        if (empty($this->password))
        {
            logger("Password was empty.");
            return false;
        }

        // TODO: Check beforehand for non-unique id
        if (empty($this->email))
        {
            logger("E-mail was empty.");
            return false;
        }

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO Users VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sssssssi", $id,$name,$surname,$username,$password,$email,$role,$confirmed))
            logger("Binding error while Adding User.");

        $id = $this->id;
        $name = $this->name;
        $surname = $this->surname;
        $username = $this->username;
        $password = $this->password;
        $email = $this->email;
        $role = $this->role;
        $confirmed = $this->confirmed;

        if (!$stmt->execute())
        {
            logger("Add user failed: " . $stmt->error);
            $stmt->close();
            CloseCon($conn);
            return false;
        }
        else
        {
            logger("Added user successfully.");
            $stmt->close();
            CloseCon($conn);
            return true;
        }


    }

    public static function removeFromDB(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM Users WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
            logger("Remove User failed " . $stmt->error);
        else
            logger("Removed user successfully!");

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

        $sql_str = "SELECT ID FROM Users WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);
        $id = $this->id;

        if (!$stmt->execute())
            logger("Check UID failed " . $stmt->error);

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
