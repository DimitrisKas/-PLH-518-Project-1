
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

    const USER_ID_PREFIX = "u";

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
            echo "ID was empty\n";
            return false;
        }
        if (empty($this->username))
        {
            echo "Username was empty\n";
            return false;
        }
        if (empty($this->password))
        {
            echo "Password was empty\n";
            return false;
        }

        // TODO: Check beforehand for non-unique id
        if (empty($this->email))
        {
            echo "E-mail was empty\n";
            return false;
        }

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO Users VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sssssssi", $id,$name,$surname,$username,$password,$email,$role,$confirmed))
            echo "Binding error while Adding User\n";

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
            echo "Add user failed: " . $stmt->error . "\n";
            $stmt->close();
            CloseCon($conn);
            return false;
        }
        else
        {
            echo "Added user successfully!\n";
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
            echo "Remove User failed " . $stmt->error;
        else
            echo "Removed user successfully!";

        $stmt->close();

        CloseCon($conn);
    }

    private function generateID()
    {
        do {
            $this->id = getRandomString(9, User::USER_ID_PREFIX);
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
            echo "Check UID failed " . $stmt->error . "\n";

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
