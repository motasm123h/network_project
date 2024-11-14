<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Models\Files\FileExport;

class FilesExportController extends Controller
{
    private $repoExportFile;
    public function __construct()
    {
        return [
            $this->repoExportFile = new FileExport(),
        ];
    }

    public function exportFileReportToPdf(int $id, int $type)
    {
        return $this->repoExportFile->exportFileReportToPdf($id, $type);
    }

    public function exportFileReportToCsv(int $id, int $type)
    {
        return $this->repoExportFile->exportFileReportToCsv($id, $type);
    }
    function compareFiles($file1, $file2)
    {
        $file1Contents = file_get_contents($file1);
        $file2Contents = file_get_contents($file2);

        // Split the contents of both files into lines
        $file1Lines = explode("\n", $file1Contents);
        $file2Lines = explode("\n", $file2Contents);

        // Create an array to store the differences
        $differences = array();


        if (count($file1Lines) > count($file2Lines)) {
            foreach ($file1Lines as $lineNum => $line) {
                // Check if the line exists in the other file
                if (!in_array($line, $file2Lines)) {
                    // If the line doesn't exist in the other file, add it to the differences array
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        // 'file' => $file1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        } else if (count($file1Lines) < count($file2Lines)) {
            foreach ($file2Lines as $lineNum => $line) {
                // Check if the line exists in the other file
                if (!in_array($line, $file1Lines)) {
                    // If the line doesn't exist in the other file, add it to the differences array
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        'file' => $file1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        } else {
            foreach ($file2Lines as $lineNum => $line) {
                // Check if the line exists in the other file
                if (!in_array($line, $file1Lines)) {
                    // If the line doesn't exist in the other file, add it to the differences array
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        // 'file' => $file1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        }

        // // Loop through the lines of both files
        // foreach ($file1Lines as $lineNum => $line) {
        //     // Check if the line exists in the other file
        //     if (!in_array($line, $file2Lines)) {
        //         // If the line doesn't exist in the other file, add it to the differences array
        //         $differences[] = array(
        //             'line' => $lineNum + 1,
        //             'file' => $file1,
        //             'type' => 'added'
        //         );
        //     }
        // }

        // foreach ($file2Lines as $lineNum => $line) {
        //     // Check if the line exists in the other file
        //     if (!in_array($line, $file1Lines)) {
        //         // If the line doesn't exist in the other file, add it to the differences array
        //         $differences[] = array(
        //             'line' => $lineNum + 1,
        //             'file' => $file2,
        //             'type' => 'removed'
        //         );
        //     }
        // }

        return $differences;
    }

    public function getdd()
    {
        // $lines1 = file('C:\Users\User\Desktop\test.docx', FILE_IGNORE_NEW_LINES);
        // $lines2 = file('C:\Users\User\Desktop\test - Copy.docx', FILE_IGNORE_NEW_LINES);
        // $lines2 = file('C:\Users\User\Desktop\m - Copy.pdf', FILE_IGNORE_NEW_LINES);
        // $lines2 = file('b.txt', FILE_IGNORE_NEW_LINES);

        // $result = array_diff($lines1, $lines2);
        // print_r(
        //     $result
        // );


        $left = 'C:\Users\User\Desktop\dating_2.docx';
        $right = 'C:\Users\User\Desktop\m - Copy.docx';
        $differences = $this->compareFiles($left, $right);
        foreach ($differences as $difference) {
            echo $difference['line'] . ' '  . $difference['type'] . "\n";
            // echo $difference['line'] . ' ' . $difference['file'] . ' ' . $difference['type'] . "\n";
        }

        // $diff = (new \Baraja\DiffGenerator\SimpleDiff)->compare($left, $right, true);
        // // dd($diff);
        // // simple render diff
        // echo '<code><pre>'
        //     . htmlspecialchars((string) $diff)
        //     . '</pre></code>';
    }
}
