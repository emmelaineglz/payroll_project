<?php
declare(strict_types=1);

//namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Certificate
{
    /**
     * @param string $fileKey
     * @param string $password
     * @return string
     *
     * @throws \Exception
     */
    public function makeKeyPem(string $fileKey, string $password, string $rfc): string
    {
        if (!file_exists($fileKey)) {
            throw new \Exception("The file {$fileKey} not exists");
        }

        if(!file_exists("../uploads/{$rfc}")){
          mkdir("../uploads/{$rfc}", 0777, true);
        }

        $process = new Process("openssl pkcs8 -inform DER -in {$fileKey} -out ../uploads/{$rfc}/{$rfc}_K.pem -passin pass:{$password}");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @param string $fileCer
     * @return string
     *
     * @throws \Exception
     */
    public function makeCerPem(string $fileCer, string $rfc ): string
    {
        if (!file_exists($fileCer)) {
            throw new \Exception("The file {$fileCer} not exists");
        }
        if(!file_exists("../uploads/{$rfc}")){
          mkdir("../uploads/{$rfc}", 0777, true);
        }

        $process = new Process("openssl x509 -inform DER -outform PEM -in {$fileCer} -out ../uploads/{$rfc}/{$rfc}_C.pem");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @param string $fileCerPem
     * @return string
     *
     * @throws \Exception
     */
    public function getSerial(string $fileCerPem = ''): string
    {
        if (!file_exists($fileCerPem)) {
            throw new \Exception("The file {$fileCerPem} not exists");
        }

        $process = new Process("openssl x509 -in {$fileCerPem} -noout -serial");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $serial = str_replace(["serial=", "\n"], "", $output);

        return $this->parseSerialNumber($serial);
    }

    /**
     * @param string $fileCerPem
     * @return Carbon
     *
     * @throws \Exception
     */
    public function getInitDate(string $fileCerPem = ''): Carbon
    {
        if (!file_exists($fileCerPem)) {
            throw new \Exception("The file {$fileCerPem} not exists");
        }

        $process = new Process("openssl x509 -in {$fileCerPem} -noout -startdate");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $initDate = str_replace(["notBefore=", "\n"], "", $output);

        $initDate = new Carbon($initDate);

        return $initDate;
    }

    /**
     * @param string $fileCerPem
     * @return Carbon
     *
     * @throws \Exception
     */
    public function getEndDate(string $fileCerPem = ''): Carbon
    {
        if (!file_exists($fileCerPem)) {
            throw new \Exception("The file {$fileCerPem} not exists");
        }

        $process = new Process("openssl x509 -in {$fileCerPem} -noout -enddate");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $endDate = str_replace(["notAfter=", "\n"], "", $output);

        $endDate = new Carbon($endDate);

        return $endDate;
    }

    /**
     * @param string $serial
     *
     * @return string
     */
    private function parseSerialNumber(string $serial): string
    {
        $output = '';
        for ($i = 0; $i < strlen($serial); $i++) {
            if ($i % 2 !== 0) {
                $output .= $serial[$i];
            }
        }

        return $output;
    }
}
