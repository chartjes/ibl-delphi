<?php

namespace IBL;

class Transaction
{
    protected $_conn;

    public function __construct($conn)
    {
        $this->_conn = $conn;
    }

    public function getCurrent()
    {
        $sql = "SELECT MAX(transaction_date) FROM transaction_log";
        $sth = $this->_conn->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();
        $maxDate = $row['max'];
        $minDate = date('Y-m-d', strtotime("{$maxDate} - 3 weeks"));
        $sql = "
            SELECT *
            FROM transaction_log
            WHERE log_entry LIKE 'Trades%'
            AND transaction_date >= '{$minDate}'
            AND transaction_date <= '{$maxDate}'
            ORDER BY trans_id, transaction_date
            ";
        $sth = $this->_conn->prepare($sql);
        $sth->execute();
        $rows = $sth->fetchAll();
        $transactionCount = count($rows);
        $currentTransactions = array();

        // The transactions we need to build are in pairs
        $idx = 0;

        while ($idx < $transactionCount) {
            $transactionId = $rows[$idx]['trans_id'];
            $team1 = $rows[$idx]['ibl_team'];
            $team2 = $rows[$idx + 1]['ibl_team'];
            $description = $team1 . ' ' . $rows[$idx]['log_entry'];
            $transactionDate = $rows[$idx]['transaction_date'];
            $currentTransactions[] = array(
                'id' => $transactionId,
                'tradePartner1' => $team1,
                'tradePartner2' => $team2,
                'description' => $description,
                'date' => $transactionDate
            );
            $idx = $idx + 2;
        }
        
        return json_encode($currentTransactions);
    }
}
