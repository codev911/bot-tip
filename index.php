<?php
    define('TOKEN','674150447:AAEX_Td-jjo_r6P_1L6CgoFIBTVXJnn7AQ0');
    $token = 'BEL';

    class Core{
        private $conn = NULL;
        private $query;
        private $dt = array();

        public function __construct(){
            $this->openConn();
        }

        public function openConn(){
            $this->conn = mysqli_connect('localhost','root','','pay_db');
        }

        public function randomid($long){
            $char = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $string = "";
            for($i = 0; $i < $long; $i++){
                $pos = rand(0, strlen($char)-1);
                $string .= $char{$pos};
            }
                return $string;
        }

        public function closeConn(){
            if(isset($this->conn)){
                $this->conn->close();
                unset($this->conn);
            }
        }

        public function allData($tableName){
            $this->query = "SELECT * FROM $tableName";
            $open = mysqli_query($this->conn, $this->query);
            
            $index = 0;
            while ($row = mysqli_fetch_assoc($open)){
                $this->dt[$index] = $row;
                $index++;
            }
        }
        
        public function selectData($tableName, $colm, $dataselect){
            $this->query = "SELECT * FROM $tableName WHERE $colm = $dataselect";
            $open = mysqli_query($this->conn, $this->query);
            $index = 0;
            while ($row = mysqli_fetch_assoc($open)){
                $this->dt[$index] = $row;
                $index++;
            }
        }

        public function deleteData($tableName, $colm, $dataselect){
            $this->query = "DELETE FROM $tableName WHERE $colm = $dataselect";
            $open = mysqli_query($this->conn, $this->query);
        }

        public function insertData($tableName ,$dt){
            $this->query = "INSERT INTO " . $tableName . " ("; 

            foreach($dt as $key => $value){
                $this->query .= $key . ",";
            }
            $this->query = substr($this->query, 0, -1);
            $this->query = substr_replace($this->query, ")", strlen($this->query), 1);
            $this->query .= " VALUES ('";

            foreach($dt as $key => $value){
                $this->query .= $value . "','";
            }
            $this->query = substr($this->query, 0, -2);
            $this->query = substr_replace($this->query, ")", strlen($this->query), 1);

            $open = mysqli_query($this->conn, $this->query);
        }

        public function editData($tableName ,$dt, $dataselect){
            $this->query = "UPDATE " . $tableName . " SET "; 

            foreach($dt as $key => $value){
                $this->query .= $key . "='" . $value . "',";
            }
            $this->query = substr($this->query, 0, -1);
            $this->query .= " WHERE ";
            foreach ($dataselect as $item => $value) {
                $this->query .= $item . " = " . $value;
            }
            
            $open = mysqli_query($this->conn, $this->query);
        }

        public function getData(){
            return $this->dt;
        }
    }

    $db = new Core();

    function BotKirim($perintah){
        return 'https://api.telegram.org/bot'.TOKEN.'/'.$perintah;
    }
 
    /* Fungsi untuk mengirim "perintah" ke Telegram
     * Perintah tersebut bisa berupa
     *  -SendMessage = Untuk mengirim atau membalas pesan
     *  -SendSticker = Untuk mengirim pesan
     *  -Dan sebagainya, Anda bisa memm
     * 
     * Adapun dua fungsi di sini yakni pertama menggunakan
     * stream dan yang kedua menggunkan curl
     * 
     * */
    function KirimPerintahStream($perintah,$data){
         $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents(BotKirim($perintah), false, $context);
        return $result;
    }
    
    function KirimPerintahCurl($perintah,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,BotKirim($perintah));
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        $kembali = curl_exec ($ch);
        curl_close ($ch);
 
        return $kembali;
    }
 
    /*  Perintah untuk mendapatkan Update dari Api Telegram.
     *  Fungsi ini menjadi penting karena kita menggunakan metode "Long-Polling".
     *  Jika Anda menggunakan webhooks, fungsi ini tidaklah diperlukan lagi.
     */
     
    function DapatkanUpdate($offset) 
    {
        //kirim ke Bot
        $url = BotKirim("getUpdates")."?offset=".$offset;
        //dapatkan hasilnya berupa JSON
        $kirim = file_get_contents($url);
        //kemudian decode JSON tersebut
        $hasil = json_decode($kirim, true);
        if ($hasil["ok"]==1)
            {
                /* Jika hasil["ok"] bernilai satu maka berikan isi JSONnya.
                 * Untuk dipergunakan mengirim perintah balik ke Telegram
                 */
                return $hasil["result"];
            }
        else
            {   /* Jika tidak maka kosongkan hasilnya.
                 * Hasil harus berupa Array karena kita menggunakan JSON.
                 */
                return array();
            }
    }
 
    function JalankanBot()
        {
            $update_id  = 0; //mula-mula tepatkan nilai offset pada nol
         
            //cek file apakah terdapat file "last_update_id"
            if (file_exists("last_update_id")) {
                //jika ada, maka baca offset tersebut dari file "last_update_id"
                $update_id = (int)file_get_contents("last_update_id");
            }
            //baca JSON dari bot, cek dan dapatkan pembaharuan JSON nya
            $updates = DapatkanUpdate($update_id);
                    
            foreach ($updates as $message)
            {
                    $update_id = $message["update_id"];;
                    $message_data = $message["message"];
                    
                    //jika terdapat text dari Pengirim
                    if (isset($message_data["text"])) {
                        $chatid = $message_data["chat"]["id"];
                        $message_id = $message_data["message_id"];
                        $text = $message_data["text"];

                        if($text == "/start"){
                            $querydata = array(
                                'userid' => $chatid,
                                'balance'=> 0
                            );
                            $db->insertData('kelas_aktif', $querydata);
                        }elseif($text == "/withdraw"){
                            $data = array(
                                'chat_id' => $chatid,
                                'text'=> 'Wrong format, example :
/withdraw<space>iAQQQo21miExetJFJPtYbkX8zRzztLL3pY<space>1',
                                'parse_mode'=>'Markdown',
                                'reply_to_message_id' => $message_id
                            );
                            KirimPerintahCurl('sendMessage',$data);
                        }else if($text == "/tip"){
                            $data = array(
                                'chat_id' => $chatid,
                                'text'=> 'Wrong format, example :
/tip<space>@example<space>1',
                                'parse_mode'=>'Markdown',
                                'reply_to_message_id' => $message_id
                            );
                            KirimPerintahCurl('sendMessage',$data);
                        }
                        else if($text == "/balance"){
                            $data = array(
                                'chat_id' => $chatid,
                                'text'=> 'Your balance is 0 '.$token,
                                'parse_mode'=>'Markdown',
                                'reply_to_message_id' => $message_id
                            );
                            KirimPerintahCurl('sendMessage',$data);
                        }
                        else if($text == "/commandlist"){
                            $data = array(
                                'chat_id' => $chatid,
                                'text'=> 'Command List:
/tip - Send '.$token.' to other member.
/balance - Check amount of your '.$token.' balance.
/withdraw - Withdraw '.$token.' balance to '.$token.' Wallet.
/commandlist - Show this command.',
                                'parse_mode'=>'Markdown',
                                'reply_to_message_id' => $message_id
                            );
                            KirimPerintahCurl('sendMessage',$data);
                        }
                    }
                    
            }
            //tulis dan tandai updatenya yang nanti digunakan untuk nilai offset
            file_put_contents("last_update_id", $update_id + 1);
        }
        
    while(true){
        sleep(1);
        JalankanBot();
    }
