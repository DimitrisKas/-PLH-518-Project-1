
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
            logger("[USER_DB] ID was empty.");
            return false;
        }
        if (empty($this->username))
        {
            logger("[USER_DB] Username was empty.");
            return false;
        }
        if (empty($this->password))
        {
            logger("[USER_DB] Password was empty.");
            return false;
        }
        if (empty($this->email))
        {
            logger("[USER_DB] E-mail was empty.");
            return false;
        }

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO Users VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sssssssi", $id,$name,$surname,$username,$password,$email,$role,$confirmed))
            logger("[USER_DB] Binding error while Adding User.");

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
            logger("[USER_DB] Add user failed: " . $stmt->error);
            $stmt->close();
            CloseCon($conn);
            return false;
        }
        else
        {
            logger("[USER_DB] Added user successfully.");
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
            logger("[USER_DB] Check UID failed " . $stmt->error);

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
            logger("[USER_DB] Remove User failed " . $stmt->error);
        else
            logger("[USER_DB] Removed user successfully!");

        $stmt->close();

        CloseCon($conn);
    }

    public static function getAllUsers()
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT * FROM Users";
        $stmt = $conn->prepare($sql_str);

        if (!$stmt->execute())
            logger("[USER_DB] Get users failed " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("[USER_DB] Found " . $num_of_rows . " users.");

        while ($row = $result->fetch_assoc()) {
            $msg = 'ID: '.$row['ID'] . ', Username: '. $row['USERNAME'] . ', Role: '. $row['ROLE'];
            logger('[USER_DB] '.$msg);
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
            logger("[USER_DB] Login User statment bind failed: " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("[USER_DB] Found " . $num_of_rows . " users.");

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
            logger("[USER_DB] Couldn't authenticate user: ". $username);

            $stmt->free_result();
            $stmt->close();
            CloseCon($conn);
            return false;
        }


    }
}
