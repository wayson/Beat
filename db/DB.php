<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/24/2015
 * Time: 9:05 PM
 */
class DB
{
    private $dbConnection;

    private $dbHost = 'localhost';
    private $dbName = 'beat';
    private $dbUserName = 'root';
    private $dbPassword = '';

    public function __construct()
    {
        $this->dbConnection = new mysqli($this->dbHost, $this->dbUserName, $this->dbPassword, $this->dbName);

        if ($this->dbConnection->connect_error) {
            die("Connection failed: " . $this->dbConnection->connect_error);
        }
    }

    public function insertLawn($lawn)
    {
        $sql = sprintf("INSERT INTO lawn (width, height) VALUES (%u, %u)", $lawn['width'], $lawn['height']);

        if($this->dbConnection->query($sql) === TRUE)
        {
            $id = $this->dbConnection->insert_id;
            return $id;
        }
        else
        {
            return -1;
        }
    }

    public function deleteLawnById($id)
    {
        $lawn_sql = "DELETE FROM lawn WHERE id=$id";
        $mower_sql = "DELETE FROM mower WHERE lawn_id=$id";

        if($this->dbConnection->query($mower_sql) === TRUE && $this->dbConnection->query($lawn_sql) === TRUE)
        {
            return array('status'=> 'ok');
        }
        else
        {
            return array('error_code' => '500', 'message' => 'Error deleting record: ' . $this->dbConnection->error);
        }
    }

    public function getLawnById($id)
    {
        $lawn = null;
        $sql = "SELECT * FROM lawn where id=$id";
        $result = $this->dbConnection->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $lawn = array(
                    'id' => $row['id'],
                    'width' => $row['width'],
                    'height' => $row['height'],
                    'mowers' => array()
                );
                break;
            }
            if(!empty($lawn))
            {
                $sql = "SELECT * FROM mower where lawn_id=$id";
                $result = $this->dbConnection->query($sql);
                if($result->num_rows > 0)
                {
                    while($row = $result->fetch_assoc())
                    {
                        $mower = array(
                            'id' => $row['id'],
                            'x' => $row['x'],
                            'y' => $row['y'],
                            'heading' => $row['heading'],
                            'commands' => $row['commands']
                        );
                        $lawn['mowers'][] = $mower;
                    }
                }
            }
            return $lawn;
        } else {
            return null;
        }
    }

    public function insertMower($mower)
    {
        $sql = sprintf(
            "INSERT INTO mower (x,y,heading,commands,lawn_id) VALUES (%u, %u, '%s', '%s', %u)",
            $mower['x'], $mower['y'], $mower['heading'], $mower['commands'], $mower['lawn_id']
        );

        if($this->dbConnection->query($sql) === TRUE)
        {
            $id = $this->dbConnection->insert_id;
            return $id;
        }
        else
        {
            return -1;
        }
    }

    public function getMowerByIdAndLawnId($id, $lawn_id)
    {
        $mower = null;
        $sql = "SELECT * FROM mower where id=$id AND lawn_id=$lawn_id";
        $result = $this->dbConnection->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $mower = array(
                    'id' => $row['id'],
                    'x' => $row['x'],
                    'y' => $row['y'],
                    'heading' => $row['heading'],
                    'commands' => $row['commands']
                );
                break;
            }

            return $mower;
        }
        else
        {
            return null;
        }
    }

    public function updateMower($mower)
    {
        $sql = sprintf(
            "UPDATE mower set x=%u,y=%u,heading='%s',commands='%s' WHERE id=%u AND lawn_id=%u",
            $mower['x'], $mower['y'], $mower['heading'], $mower['commands'], $mower['id'], $mower['lawn_id']
        );

        if($this->dbConnection->query($sql) === TRUE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function deleteMowerByIdAndLawnId($id, $lawn_id)
    {
        $sql = "DELETE FROM mower WHERE lawn_id=$lawn_id AND id=$id";

        if($this->dbConnection->query($sql) === TRUE)
        {
            return array('status'=> 'ok');
        }
        else
        {
            return array('error_code' => '500', 'message' => 'Error deleting record: ' . $this->dbConnection->error);
        }
    }

    public function closeDB()
    {
        $this->dbConnection->close();
    }

}