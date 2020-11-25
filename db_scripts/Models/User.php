
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

    // static functions
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

    public static function getAllUsers()
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT * FROM Users";
        $stmt = $conn->prepare($sql_str);

        if (!$stmt->execute())
            logger("Get users failed " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("Found " . $num_of_rows . " users.");

        while ($row = $result->fetch_assoc()) {
            $msg = 'ID: '.$row['ID'] . ', Username: '. $row['USERNAME'] . ', Role: '. $row['ROLE'];
            logger($msg);
        }

        $stmt->free_result();
        $stmt->close();

        CloseCon($conn);
    }


    /**
     * Tries to login a user based on given Usernam and Password.
     * On Success, returns User model.
     * On Failure, returns false.
     * @param $username
     * @param $password
     * @return false|User
     */
    public static function LoginUser($username, $password)
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT * FROM Users WHERE USERNAME=? AND PASSWORD=?";
        $stmt = $conn->prepare($sql_str); $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("ss",$_username, $_password);
        $_username = $username;
        $_password = $password;

        if (!$stmt->execute())
            logger("Login User statment bind failed: " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("Found " . $num_of_rows . " users.");

        if ($num_of_rows === 1)
        {
            $row = $result->fetch_assoc();
            $user = new User($row['NAME'], $row['SURNAME'], $row['USERNAME'], $row['PASSWORD'], $row['EMAIL'], $row['ROLE'] ,$row['CONFIRMED']);
            $user->id = $row['ID'];

            $stmt->free_result();
            $stmt->close();
            CloseCon($conn);

            return $user;
        }
        else
        {
            logger("Couldn't authenticate user: ". $username);

            $stmt->free_result();
            $stmt->close();
            CloseCon($conn);
            return false;
        }


    }
}
