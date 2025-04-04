<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DepositStatus;
use App\Enums\KYCStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Services\Admin\AffiliateService;
use App\Http\Services\Admin\CreditReportService;
use App\Http\Services\Admin\DepositReportService;
use App\Http\Services\Admin\KycService;
use App\Http\Services\Admin\SubscriptionReportService;
use App\Http\Services\Admin\TemplateActivityService;
use App\Http\Services\Admin\TransactionService;
use App\Http\Services\Admin\WebhookService;
use App\Http\Services\Admin\WithdrawReportService;
use App\Http\Services\PaymentService;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use App\Models\CreditLog;
use App\Traits\ModelAction;
use Illuminate\Support\Arr;
use App\Traits\Notifyable;


class ActivityHistoryController extends Controller
{


    use ModelAction ,Notifyable;

    public function __construct(
        protected TransactionService $transactionService,
        protected TemplateActivityService $templateActivityService,
        protected CreditReportService $creditReportService,
        protected SubscriptionReportService $subscriptionReportService,
        protected DepositReportService $depositReportService,
        protected PaymentService $paymentService,
        protected WithdrawReportService $withdrawReportService,
        protected AffiliateService $affiliateService,
        protected KycService $kycService,
        protected WebhookService $webhookService
    )
    {


        $this->middleware(['permissions:view_report'])
            ->only([
                    "templateReport",
                    'creditReport',
                    'transactionReport',
                    'subscriptionReport',
                    'updateSubscription',
                    "depositReport",
                    "updateDeposit",
                    "depositDetails",
                    'withdrawReport',
                    'withdrawUpdate',
                    'withdrawDetails',
                    'kycReport',
                    'kycDetails',
                    'kycUpdate',
                    'webhookReport'
                ]);

        $this->middleware(['permissions:delete_report'])
             ->only([
                    "templateReportdestroy",
                    'creditReportDestory',
                    'creditReportBulk',
                    'destroyTransaction',
                    'transactionBulk',
                    'depostiBulk',
                    'destroyDeposit',
                    'destroyWebhook'
                ]);
    }



    /**
     * Templates report
     *
     * @return View
     */
    public function templateReport() :View{
        return view('admin.report.template_report',$this->templateActivityService->getReport());
    }


    /**
     * @param string|int $id
     * @return RedirectResponse
     */
    public function templateReportdestroy(string|int $id): RedirectResponse{
        $response = $this->templateActivityService->destroy($id);
        return  back()->with(response_status('Item deleted succesfully'));
    }


    /**
     * Credit report
     *
     * @return View
     */
    public function creditReport(): View{
        return view('admin.report.credit_report',$this->creditReportService->getReport());
    }


    /**
     * @param int|string $id
     * @return RedirectResponse
     */
    public function creditReportdestroy(int|string $id): RedirectResponse{

        $response = $this->creditReportService->destroy($id);
        return  back()->with(response_status('Item deleted succesfully'));
    }



    /**
     * Bulk action of credit log
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function creditReportBulk(Request $request): RedirectResponse{

        try {
            $response =  $this->bulkAction($request,["model"=> new CreditLog()]);
        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }

    /**
     * Get all transaction report
     *
     * @return View
     */
    public function transactionReport(): View{
        return view('admin.report.transaction_report',$this->transactionService->getReport());
    }


    /**
     * @param int|string $id
     * @return RedirectResponse
     */
    public function destroyTransaction(int|string $id): RedirectResponse{
        $response = $this->transactionService->destroy($id);
        return  back()->with(response_status('Deleted Successfully'));
    }


    /**
     * Bulk action of transaction log
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function transactionBulk(Request $request): RedirectResponse{

        try {
            $response =  $this->bulkAction($request,["model"=> new Transaction()]);
        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }



    /**
     * Get all subscription Log
     *
     * @return View
     */
    public function subscriptionReport() :View{
        return view('admin.report.subscription_report',$this->subscriptionReportService->getReport());
    }


    /**
     * Update  subscription
     *
     */
    public function updateSubscription(Request $request): RedirectResponse{

        $request->validate([
            'id'         => ["required","exists:subscriptions,id"],
            "status"     => ["required",Rule::in(SubscriptionStatus::toArray())],
            "expired_at" => ["required",'date'],
        ]);
        $response = $this->subscriptionReportService->updateSubscription($request);
        return  back()->with(response_status(Arr::get($response ,'message')));
    }


    /**
     * Deposit statistics
     *
     * @return View
     */
    public function depositReport(): View{
        return view('admin.report.deposit_report',$this->depositReportService->getReport());
    }



    /**
     * Get deposit details
     *
     * @param integer|string $id
     * @return View
     */
    public function depositDetails(int|string $id): View{
        return view('admin.report.deposit_details',[
            'breadcrumbs'     =>  ['Home'=>'admin.home',"Deposits"=> "admin.deposit.report.list",'Deposit Details'=> null],
            'title'           => 'Deposit Details',
            "report"          =>  $this->depositReportService->getSpecificReport($id)
        ]);
    }



    /**
     * Update a deposit log
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateDeposit(Request $request): RedirectResponse{

        $request->validate([
            "id"        => ['required',"exists:payment_logs,id"],
            "status"    => ['required',Rule::in([DepositStatus::value('REJECTED'),DepositStatus::value('PAID')])],
            "feedback"  => ['required',"string",'max:255'],
        ]);

        $response =  $this->paymentService->handleDepositRequest(
            $this->depositReportService->getSpecificReport($request->input("id"),DepositStatus::PENDING), //pending log
            $request->except(['id','token']) // request array
        );

        return back()->with(response_status(Arr::get($response,"message",translate('Fail to update')),$response['status']? "success":"error"));
    }




    /**
     * Withdraw report
     *
     * @return View
     */
    public function withdrawReport(): View{
        return view('admin.report.withdraw_report',$this->withdrawReportService->getReport());
    }


    /**
     * Withdraw details
     *
     * @param integer|string $id
     * @return View
     */
    public function withdrawDetails(int|string $id): View{
        return view('admin.report.withdraw_details',[
            'breadcrumbs'     =>  ['Home'=>'admin.home',"Withdraws"=> "admin.withdraw.report.list",'Withdraw Details'=> null],
            'title'           => 'Withdraw details',
            "report"          =>  $this->withdrawReportService->getSpecificReport($id)
        ]);
    }



    /**
     * Update a specific withdraw log
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function withdrawUpdate(Request $request): RedirectResponse{

        $request->validate([
            "id"        => ['required',"exists:withdraw_logs,id"],
            "status"    => ['required',Rule::in([WithdrawStatus::value('APPROVED'),WithdrawStatus::value('REJECTED')])],
            "feedback"  => ['required',"string",'max:255'],
        ]);

        $report = $this->withdrawReportService->getSpecificReport($request->input("id"),WithdrawStatus::PENDING);

        $response =  response_status(
                                "Insufficient funds in user account. Withdrawal request cannot be processed due to insufficient balance.",'error');
        // check user and balance
        if($report->user &&
           $report->user->balance > $report->base_final_amount
           ){
            $response =  $this->paymentService->handleWithdrawRequest($report ,$request->except(['id','token']));
            $response =  response_status(Arr::get($response,"message",translate('Fail to update')) ,$response['status']? "success":"error" );
        }
        return back()->with($response);
    }


    /**
     * Affiliate report and statistics
     * @return View
     */
    public function affiliateReport(): View{
        return view('admin.report.affiliate_report',$this->affiliateService->getReport());
    }

    /**
     * KYC Report and statistics
     *
     * @return View
     */
    public function kycReport(): View{
        return view('admin.report.kyc_report',$this->kycService->getReport());
    }

    /**
     * Get KYC Details View
     *
     * @param integer|string $id
     * @return View
     */
     public function kycDetails(int|string $id): View{

        return view('admin.report.kyc_details',[
            'breadcrumbs'     => ['Home'=>'admin.home','KYC Logs'=> "admin.kyc.report.list" ,'Details' => null],
            'title'           => 'KYC Details',
            "report"          => $this->kycService->getSpecificReport($id)
        ]);
    }


    /**
     * Update KYC details
     *
     * @param Request $request
     * @return RedirectResponse
     */
     public function kycUpdate(Request $request): RedirectResponse{
        $request->validate([
            "id"        => ['required',"exists:kyc_logs,id"],
            "status"    => ['required',Rule::in([KYCStatus::value('APPROVED'),KYCStatus::value('REJECTED')])],
            "notes"     => ['required',"string",'max:255'],
        ]);

        $report   = $this->kycService->getSpecificReport($request->input("id"),KYCStatus::REQUESTED);
        $response = $this->kycService->update($report ,$request->except(['_token']));
        return back()->with(response_status("Updated successfully"));

    }



    /**
     * Get webhook report
     *
     * @return View
     */
    public function webhookReport(): View{
        return view('admin.report.webhook_report',$this->webhookService->getReport());
    }


    /**
     * Destroy a specific webhook
     *
     * @param integer|string $id
     * @return RedirectResponse
     */
     public function destroyWebhook(int|string $id): RedirectResponse{
        $response = $this->webhookService->destroy($id);
        return  back()->with(response_status('Deleted Successfully'));
    }
}
