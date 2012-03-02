<?php

namespace IBL;

class Transaction
{
    protected $_conn;

    public function __construct($conn)
    {
        $this->_conn = $conn;
    }

    public function getArchived()
    {
        $maxDate = $this->_getMaxDate();
        $archiveMaxDate = date('Y-m-d', strtotime("{$maxDate} - 6 months"));
        $archiveMinDate = date('Y-m-d', strtotime("{$archiveMaxDate} - 3 weeks"));
        $rows = $this->_getRawData($archiveMinDate, $archiveMaxDate);
        $archivedTransactions = $this->_generateFormattedResults($rows);

        return json_encode($archivedTransactions);
    }

    public function getCurrent()
    {
        $maxDate = $this->_getMaxDate();
        $minDate = date('Y-m-d', strtotime("{$maxDate} - 3 weeks"));

        // The transactions we need to build are in pairs
        $rows = $this->_getRawData($minDate, $maxDate);
        $currentTransactions = $this->_generateFormattedResults($rows);
        
        return json_encode($currentTransactions);
    }

    protected function _generateFormattedResults($rows)
    {
        $idx = 0;
        $transactionCount = count($rows);
        
        if ($transactionCount == 0) {
            return array();
        }

        $results = array();

        while ($idx < $transactionCount) {
            $transactionId = $rows[$idx]['trans_id'];
            $team1 = $rows[$idx]['ibl_team'];
            $team2 = $rows[$idx + 1]['ibl_team'];
            $description = $team1 . ' ' . $rows[$idx]['log_entry'];
            $transactionDate = $rows[$idx]['transaction_date'];
            $results[] = array(
                'id' => $transactionId,
                'tradePartner1' => $team1,
                'tradePartner2' => $team2,
                'description' => $description,
                'date' => $transactionDate
            );
            $idx = $idx + 2;
        }

        return $results;
    }

    protected function _getMaxDate()
    {
        $sql = "SELECT MAX(transaction_date) FROM transaction_log";
        $sth = $this->_conn->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();

        return $row['max'];
    }

    protected function _getRawData($minDate, $maxDate)
    {
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
        
        return $sth->fetchAll();
    }
}
