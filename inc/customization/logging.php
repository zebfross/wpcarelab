<?php

function my_error_handler($errno, $message, $file = null, $line = 0)
{
    log_error('my_error_handler', compact(explode(' ', "errno message file : line")), $errno, false, false /*backtrace*/);

    return false;
}

error_reporting(E_ALL | E_STRICT);

register_shutdown_function(
    function () {
        session_write_close();
        /*$err = error_get_last();
        if ($err) {
        tw_error_handler($err['type'], $err['message'], $err['file'], $err['line']);
        }*/
    }
);

class MyLogger
{
    private $log_file;

    function __construct()
    {
        $this->log_file = get_template_directory() . "/my_debug_log.log";
    }

    function __destruct()
    {
    }

    /**
     * Prints a message to the debug file that can easily be called by any subclass.
     *
     * @param mixed $message      - an object, array, string, number, or other data to write to the debug log
     * @param bool  $shouldNotDie - whether or not the The function should exit after writing to the log
     */
    public function log_info($message, $type)
    {
        $output = '';
        //Timestamp it
        $output .= '[' . date('m/d/Y g:i:s A') . '] - ';

        //Flag failure (if applicable)
        $output .= "$type: ";

        //Final debug output msg
        $output = $output . print_r($message, true /*return*/);

        error_log(print_r($message, true));
        if (! file_put_contents($this->log_file, $output . "\r\n", FILE_APPEND) ) {
            return false;
        }
    }
}

$logger = new MyLogger();

function print_html($arg)
{
    echo '<pre>' . esc_html(print_r($arg, true)) . '</pre>';
}

function log_info($message, $print = true, $type="INFO")
{
    global $logger;
    if ($print && ((WP_DEBUG && WP_DEBUG_DISPLAY) || ini_get('display_errors'))) {
        print_html($message);
    }
    $logger->log_info($message, $type);
}

function log_error($code, $message, $data = null, $shouldDie = false, $showBacktrace = true)
{
    $backtrace = "";
    if ($showBacktrace) {
        $e = new Exception();
        $backtrace = $e->getTraceAsString();
    }
    log_info(compact(explode(' ', 'code message data shouldDie backtrace')), false, "ERROR");

    //GoogleAnalytics::sendError(
    //    'error',
    //    $code,
    //    null /*path*/,
    //    $data,
    //    print_r($message, true),
    //    $shouldDie /*isFatal*/
    //);

    if ($shouldDie) {
        exit;
    }
}

function print_queries()
{
    if ((defined('SAVEQUERIES') && SAVEQUERIES) && !is_admin() && !is_customize_preview()) {
        global $wpdb;
        print_html($wpdb->queries);
    }
}
