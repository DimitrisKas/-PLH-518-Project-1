
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


    public function __construct($id, $name, $surname, $username, $password, $email, $role, $confirmed)
    {
        $this->$id = $id;
        $this->$name = $name;
        $this->$surname = $surname;
        $this->$username = $username;
        $this->$password = $password;
        $this->$email = $email;
        $this->$role = $role;
        $this->$confirmed = $confirmed;
    }


    /** @noinspection SpellCheckingInspection */
    public function addToDB():bool
    {
        // @TODO check user data

        if (empty($this->id))
        {
            echo "ID was empty";
            return false;
        }
        if (empty($this->username))
        {
            echo "Username was empty";
            return false;
        }
        if (empty($this->PASSWORD))
        {
            echo "PASSWORD was empty";
            return false;
        }
        if (empty($this->EMAIL))
        {
            echo "PASSWORD was empty";
            return false;
        }




        $conn = OpenCon(true);

        $sql_str = "INSERT INTO Users (ID, NAME, SURNAME, USERNAME, PASSWORD, EMAIL, ROLE, CONFIRMED)
        VALUES(?, ?, ?, ?, ?, ?, ?, :CONFIRMED)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sssssssi", $this->id,$this->name,$this->surname,$this->username,$this->password,$this->email,$this->role,$this->confirmed))
            echo "Binding error while Adding User";

        if (!$stmt->execute())
            echo "Add user failed: " . $stmt->error;
        else
            echo "Added user successfully!";

        $stmt->close();



        if ($conn->query($sql_str) === TRUE)
            echo "User Added!\n";
        else
            echo "Error adding user" . $conn->error ."\n";

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



        if ($conn->query($sql_str) === TRUE)
            echo "User Added!\n";
        else
            echo "Error adding user" . $conn->error ."\n";
    }

}
