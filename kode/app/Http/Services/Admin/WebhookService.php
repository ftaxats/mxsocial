<?php
namespace App\Http\Services\Admin;

use App\Models\PostWebhookLog;
use App\Models\User;
use Carbon\Carbon;

class WebhookService
{


    /**
     * Get all webhook report 
     *
     * @return array
     */
    public function getReport(? User $user = null): array{

        return [
            'breadcrumbs'     => ['Home'=>'admin.home','Webhook Reports'=> null],
            'title'           => 'Webhook Reports',
            "reports"         => PostWebhookLog::whereNull('user_id')
                                                    ->date()               
                                                    ->latest()
                                                    ->paginate(paginateNumber())
                                                    ->appends(request()->all()),
        ];

    }


    /**
     * Destory a specific report
     *
     * @param integer|string $id
     * @return boolean
     */
    public function destroy(int|string $id) : bool{

        $report = PostWebhookLog::whereNull('user_id')
                                ->where('id',$id)
                                ->firstOrfail();
        $report->delete();
        return true;
    }

}
