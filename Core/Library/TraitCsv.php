<?php
namespace Core\Library;

trait TraitCsv
{

    private function _csv(array $data = [], string $filename = "export", array $excludes = [] , string $delimiter = ';', string $enclosure = '"'){
        $countExcludes = count($excludes);
        // Tells to the browser that a file is returned, with its name : $filename.csv
        header("Content-disposition: attachment; filename=$filename.csv");
        // Tells to the browser that the content is a csv file
        header("Content-Type: text/csv");

        // I open PHP memory as a file
        $fp = fopen("php://output", 'w');

        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // I add the array keys as CSV headers
        fputcsv($fp,array_keys((array)$data[0]),$delimiter,$enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            
            fputcsv($fp, (array)$fields,$delimiter,$enclosure);
        }

        // Close the file
        fclose($fp);

        // Stop the script
        die();

    }

    private function _makeCsv(array $data = [], string $filename = "export", string $delimiter = ';', string $enclosure = '"') {
        
        // Tells to the browser that a file is returned, with its name : $filename.csv
        header("Content-disposition: attachment; filename=$filename.csv");
        // Tells to the browser that the content is a csv file
        header("Content-Type: text/csv");

        // I open PHP memory as a file
        $fp = fopen("php://output", 'w');

        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // I add the array keys as CSV headers
        fputcsv($fp,array_keys((array)$data[0]),$delimiter,$enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            
            fputcsv($fp, (array)$fields,$delimiter,$enclosure);
        }

        // Close the file
        fclose($fp);

        // Stop the script
        die();

    }

    private function _saveCsv(array $data = [], string $filename = "export", string $delimiter = ';', string $enclosure = '"') {
        
        // Tells to the browser that a file is returned, with its name : $filename.csv
        //header("Content-disposition: attachment; filename=$filename.csv");
        // Tells to the browser that the content is a csv file
        //header("Content-Type: text/csv");

        // I open PHP memory as a file
        
        //$fileName   = 'Active Users '.$todayDate.'.csv';
        $filePath   = EXPORT_DIR . DS . $filename . '.csv';
        $fp = fopen($filePath, 'w');
        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // I add the array keys as CSV headers
        fputcsv($fp,array_keys((array)$data[0]),$delimiter,$enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            
            fputcsv($fp, (array)$fields,$delimiter,$enclosure);
        }

        // Close the file
        fclose($fp);

        // Stop the script
        //die();

    }
}