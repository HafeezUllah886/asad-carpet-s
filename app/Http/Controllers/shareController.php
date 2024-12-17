<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\sales;
use App\Models\transactions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class shareController extends Controller
{

    public function share($id)
    {
        $whatsBalance = getWhatsappBalance();

        if($whatsBalance < 0.006)
        {
            return "Out of Balance";
        }
        createWhatsappBalance("Invoice no. $id", now());
        $sale = sales::find($id);
        $pdf = Pdf::loadView('sales.pdf', compact('sale'));

        $time = time() . '.pdf';

        $pdfPath = 'pdfs/' . $time;

        $pdf->save(public_path($pdfPath));

        $publicUrl = asset($pdfPath);

        return $time;
    }

    public function shareStatement($id, $from, $to)
    {
        $whatsBalance = getWhatsappBalance();
        $account = accounts::find($id);
        if($whatsBalance < 0.006)
        {
            return "Out of Balance";
        }
        createWhatsappBalance("Statment of $account->title", now());

        $transactions = transactions::where('accountID', $id)->whereBetween('date', [$from, $to])->get();

        $pre_cr = transactions::where('accountID', $id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = transactions::where('accountID', $id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = transactions::where('accountID', $id)->sum('cr');
        $cur_db = transactions::where('accountID', $id)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        $pdf = Pdf::loadView('Finance.accounts.pdf', compact('account', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));

        $time = time() . '.pdf';

        $pdfPath = 'pdfs/' . $time;

        $pdf->save(public_path($pdfPath));

        $publicUrl = asset($pdfPath);

        return $time;
    }

    public function getfile($file)
    {
        $filePath = public_path('pdfs/'. $file);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        return Response::file($filePath);
    }
}
