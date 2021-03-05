<?php

namespace FluentForm\App\Modules\Entries;

use FluentForm\App\Modules\Form\FormDataParser;
use FluentForm\App\Modules\Form\FormFieldsParser;
use FluentForm\Framework\Foundation\Application;
use FluentForm\Framework\Helpers\ArrayHelper as Arr;

class Export
{
    /**
     * @var \FluentForm\Framework\Foundation\Application
     */
    protected $app;

    /**
     * @var \FluentForm\Framework\Request\Request
     */
    protected $request;

    /**
     * @var String table/data source name
     */
    protected $tableName;

    /**
     * Export constructor.
     *
     * @param \FluentForm\Framework\Foundation\Application $application
     */
    public function __construct(Application $application, $tableName = 'fluentform_submissions')
    {
        $this->app = $application;
        $this->request = $application->request;
        $this->tableName = $tableName;
    }

    /**
     * Exports form entries as CSV.
     *
     * @todo:: refactor.
     */
    public function index()
    {
        $formId = intval($this->request->get('form_id'));

        $form = wpFluent()->table('fluentform_forms')->find($formId);

        if (!$form) {
            exit('No Form Found');
        }

        $type = sanitize_text_field($this->request->get('format', 'csv'));
        if (!in_array($type, ['csv', 'ods', 'xlsx', 'json'])) {
            exit('Invalid requested format');
        }

        if ($type == 'json') {
            $this->exportAsJSON($form);
        }

        if (!defined('FLUENTFORM_DOING_CSV_EXPORT')) {
            define('FLUENTFORM_DOING_CSV_EXPORT', true);
        }

        $formInputs = FormFieldsParser::getEntryInputs($form, array('admin_label', 'raw'));

        $inputLabels = FormFieldsParser::getAdminLabels($form, $formInputs);

        $submissions = $this->getSubmissions($formId);

        $submissions = FormDataParser::parseFormEntries($submissions, $form, $formInputs);
        $exportData = [];

        foreach ($submissions as $submission) {
            $submission->response = json_decode($submission->response, true);
            $temp = [];
            
            foreach ($inputLabels as $field => $label) {
                $temp[] = trim(
                    wp_strip_all_tags(
                        FormDataParser::formatValue(
                            Arr::get($submission->user_inputs, $field)
                        )
                    )
                );
            }

            if ($form->has_payment && $this->tableName == 'fluentform_submissions') {
                $temp[] = round($submission->payment_total / 100, 1);
                $temp[] = $submission->payment_status;
                $temp[] = $submission->currency;
            }

            $temp[] = @$submission->id;
            $temp[] = @$submission->status;
            $temp[] = @$submission->created_at;

            $exportData[] = $temp;
        }

        $extraLabels = [];
        if ($form->has_payment && $this->tableName == 'fluentform_submissions') {
            $extraLabels[] = 'payment_total';
            $extraLabels[] = 'payment_status';
            $extraLabels[] = 'currency';
        }

        $extraLabels[] = 'entry_id';
        $extraLabels[] = 'entry_status';
        $extraLabels[] = 'created_at';

        $inputLabels = array_merge($inputLabels, $extraLabels);

        $data = array_merge([array_values($inputLabels)], $exportData);

        $data = apply_filters('fluentform_export_data', $data, $form, $exportData, $inputLabels);

        $fileName = sanitize_title($form->title, 'export', 'view') . '-' . date('Y-m-d');

        $this->downloadOfficeDoc($data, $type, $fileName);
    }


    private function downloadOfficeDoc($data, $type = 'csv', $fileName = null)
    {
        $data = array_map(function ($item) {
            return array_map(function ($itemValue) {
                if (is_array($itemValue)) {
                    return implode(', ', $itemValue);
                }
                return $itemValue;
            }, $item);
        }, $data);
        require_once $this->app->appPath() . 'Services/Spout/Autoloader/autoload.php';
        $fileName = ($fileName) ? $fileName . '.' . $type : 'export-data-' . date('d-m-Y') . '.' . $type;
        $writer = \Box\Spout\Writer\WriterFactory::create($type);
        $writer->openToBrowser($fileName);
        $writer->addRows($data);
        $writer->close();
        die();
    }

    private function exportAsJSON($form)
    {
        $formInputs = FormFieldsParser::getEntryInputs($form, array('admin_label', 'raw'));

        $inputLabels = FormFieldsParser::getAdminLabels($form, $formInputs);

        $submissions = $this->getSubmissions($form->id);

        $submissions = FormDataParser::parseFormEntries($submissions, $form, $formInputs);
        $exportData = [];

        foreach ($submissions as $submission) {
            $submission->response = json_decode($submission->response, true);
        }

        // header('Content-disposition: attachment; filename=' . sanitize_title($form->title, 'export', 'view') . '-' . date('Y-m-d') . '.json');
        // header('Content-type: application/json');
        // echo json_encode($submissions);
        // foreach($submissions as $data){
        //     print_r($data->response['data_pendidikan_formal']);
        // }
        // exit();
        ob_start();
        ?>
        <table border="1">
            <tr>
                <th>Informasi diperoleh dari </th>
                <th>Sebutkan nama website/media sosial/lokasi informasi lowongan kerja diperoleh</th>
                <th>Posisi yang dilamar</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Kabupaten/Kota Domisili</th>
                <th>Alamat Domisili</th>
                <th>Alamat Sesuai KTP</th>
                <th>No. HP</th>
                <th>No. WA</th>
                <th>Email</th>
                <th>Agama</th>
                <th>Status Pernikahan</th>
                <th>Tinggi Badan</th>
                <th>Jenjang</th>
                <th>Nama Sekolah/Universitas</th>
                <th>Jurusan</th>
                <th>Tahun Masuk</th>
                <th>Tahun Lulus</th>
                <th>IPK/Nilai Rata-Rata Rapor</th>
                <th>Nama Pendidikan/Sertifikasi</th>
                <th>Nama Lembaga</th>
                <th>Tahun</th>
                <th>Masa Berlaku (tahun)</th>
                <th>Nama Ketrampilan</th>
                <th>Nilai</th>
                <th>Nama Perusahaan</th>
                <th>Posisi</th>
                <th>Mulai Bekerja (Bulan/Tahun)</th>
                <th>Berhenti Bekerja (Bulan/Tahun)</th>
                <th>Alasan Berhenti Bekerja</th>
            </tr>
        <?php
        foreach($submissions as $data){
            $pendformal = [];
            $pendnonformal = [];
            $ketrampilan = [];
            $pengkerja = [];
            foreach($data->response['data_pendidikan_formal'] as $pformal){
                $payload = [
                    'jenjang' => $pformal[0],
                    'school_name' => $pformal[1],
                    'jurusan' => $pformal[2],
                    'tahun_masuk' => $pformal[3],
                    'tahun_lulus' => $pformal[4],
                    'ipk' => $pformal[5]
                ];
                array_push($pendformal, $payload);
            }
            foreach($data->response['repeater_field'] as $pnonformal){
                $payload = [
                    'pendidik' => $pnonformal[0],
                    'lembaga' => $pnonformal[1],
                    'tahun' => $pnonformal[2],
                    'berlaku' => $pnonformal[3]
                ];
                array_push($pendnonformal, $payload);
            }
            foreach($data->response['data_ketrampilan'] as $trampil){
                $payload = [
                    'ketrampilan' => $trampil[0],
                    'nilai' => $trampil[1]
                ];
                array_push($ketrampilan, $payload);
            }
            foreach($data->response['data_pengalaman_kerja'] as $kerja){
                $payload = [
                    'perusahaan' => $kerja[0],
                    'posisi' => $kerja[1],
                    'mulai' => $kerja[2],
                    'berhenti' => $kerja[3],
                    'alasan' => $kerja[4]
                ];
                array_push($pengkerja, $payload);
            }
            $jmlrow = [count($pendformal), count($pendnonformal), count($ketrampilan), count($pengkerja)];
            $besar = max($jmlrow);
            for($i=0;$i<=$besar;$i++){
        ?>
        <tr>
            <td rowspan="<?=$besar;?>"><?=$data->response['dropdown_3']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['input_text_1']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['dropdown_4']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['names']['first_name']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['input_text']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['input_radio']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['datetime']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['dropdown']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['description']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['description']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['phone']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['phone_1']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['email']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['dropdown_1']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['dropdown_2']?></td>
            <td rowspan="<?=$besar;?>"><?=$data->response['numeric-field']?></td>
            <td>Jenjang</td>
            <td>Nama Sekolah/Universitas</td>
            <td>Jurusan</td>
            <td>Tahun Masuk</td>
            <td>Tahun Lulus</td>
            <td>IPK/Nilai Rata-Rata Rapor</td>
            <td>Nama Pendidikan/Sertifikasi</td>
            <td>Nama Lembaga</td>
            <td>Tahun</td>
            <td>Masa Berlaku (tahun)</td>
            <td>Nama Ketrampilan</td>
            <td>Nilai</td>
            <td>Nama Perusahaan</td>
            <td>Posisi</td>
            <td>Mulai Bekerja (Bulan/Tahun)</td>
            <td>Berhenti Bekerja (Bulan/Tahun)</td>
            <td>Alasan Berhenti Bekerja</td>
        </tr>
        <?php }} ?>
        </table>
        <?php
        $dt = ob_get_clean();
        // header("Content-type: application/vnd.ms-excel");
        // header('Content-disposition: attachment; filename=' . sanitize_title($form->title, 'export', 'view') . '-' . date('Y-m-d') . '.xls');
        echo $dt;
        
        // exit();
    }

    private function getSubmissions($formId)
    {
        $query = wpFluent()->table($this->tableName)
            ->where('form_id', $formId)
            ->orderBy('id', $this->request->get('sort_by', 'DESC'));

        if ($this->tableName == 'fluentform_submissions') {
            $dateRange = $this->request->get('date_range');
            if ($dateRange) {
                $query->where('created_at', '>=', $dateRange[0] . ' 00:00:01');
                $query->where('created_at', '<=', $dateRange[1] . ' 23:59:59');
            }

            $isFavourite = $this->request->get('is_favourite');

            if ($isFavourite == 'yes') {
                $query->where('is_favourite', '1');
            }
            
            $status = $this->request->get('entry_type');

            if ($status == 'trashed') {
                $query->where('status', 'trashed');
            } else if ($status && $status != 'all') {
                $query->where('status', $status);
            } else {
                $query->where('status', '!=', 'trashed');
            }
            $entries = fluentFormSanitizer($this->request->get('entries', []));
    
            if (is_array($entries) && (count ($entries) > 0 )) {
                $query->whereIn('id', $entries);
            }

            if ($paymentStatuses = $this->request->get('payment_statuses')) {
                if (is_array($paymentStatuses)) {
                    $query->whereIn('payment_status', $paymentStatuses);
                }
            }

        }

        $searchString = $this->request->get('search');

        if ($searchString) {
            $query->where(function ($q) use ($searchString) {
                $q->where('id', 'LIKE', "%{$searchString}%")
                    ->orWhere('response', 'LIKE', "%{$searchString}%");

                if ($this->tableName == 'fluentform_submissions') {
                    $q->orWhere('status', 'LIKE', "%{$searchString}%")
                        ->orWhere('created_at', 'LIKE', "%{$searchString}%");
                }
            });
        }

        return $query->get();
    }
}
