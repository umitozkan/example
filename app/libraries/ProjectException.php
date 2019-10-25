<?php


class ProjectException extends Exception
{
    private $trace;
    private $orginalMessage;

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
        $this->orginalMessage = $message;
    }

    /**
     * @param array $trace
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
    }

    /**
     * @param mixed $orginalMessage
     */
    public function setOrginalMessage($orginalMessage)
    {
        $this->orginalMessage = $orginalMessage;
    }

    /**
     * @return mixed
     */
    public function getOrginalMessage()
    {
        return $this->orginalMessage;
    }

    /**
     * @return string
     */
    public function getTraceMessage()
    {
        $str = "";
        foreach ($this->trace as $k=>$item) {
            $str .= "<br/><b>$k</b> ";
            $str .= ", Class: ".$item["file"];
            $str .= ", Line: ".$item["line"];
            $str .= ", Triggered From: ".$item["class"]."->".$item["function"];
            $str .= ", Args : ".json_encode($item["args"]);
        }
        return $str;
    }
}