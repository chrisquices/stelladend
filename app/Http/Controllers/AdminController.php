<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UploadStockAnalysisDividendDataRequest;
use App\Http\Requests\Calendar\IndexCalendarRequest;
use App\Models\Security;
use App\Services\AdminService;
 use Inertia\Inertia;

class AdminController extends Controller
{
    private $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    public function index(IndexCalendarRequest $request)
    {
        $securities = Security::select('uid', 'ticker')->get();

        return Inertia::render('admin/Index', [
            'securities' => $securities
        ]);
    }

    public function uploadStockAnalysisDividendData(UploadStockAnalysisDividendDataRequest $request)
    {
        $security = Security::where('uid', $request->securityUid)->firstOrFail();

      return  $this->adminService->processStockAnalysisDividendData($security->id, $request->file('file'));

        return back();
    }
}
