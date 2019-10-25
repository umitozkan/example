<?php

	class ExceptionController extends Controller
    {

        /**
         * ExceptionController constructor.
         * @param Exception $e
         */
        public function __construct($e)
        {
            $ex = new ProjectException($e->getMessage());
            if ($e instanceof PDOException){
                $ex->setMessage(SYSTEM_ERR_MSG);
                $ex->setOrginalMessage($e->getMessage());
            }
            $ex->setTrace($e->getTrace());
            $this->view('pages/exception', ["ex" => $ex]);
        }
    }