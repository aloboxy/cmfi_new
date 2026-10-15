<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateDetail;
use App\Models\LiveClassConfig;
use App\Models\PaymentConfig;
use App\Models\SmsCredential;
use App\Models\SmsTemplate;
use App\Models\SmsTemplateDetail;
use App\Models\TransactionLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolSettingsController extends Controller
{
    private function getBranchID(Request $request): ?int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->query('branch_id') ?? $request->input('branch_id');
            if ($bid) return (int) $bid;
            // fallback to session branch or first branch
            $fb = $request->session()->get('branch_id');
            if ($fb) return (int) $fb;
            $first = Branch::orderBy('id')->value('id');
            return $first ? (int) $first : get_loggedin_branch_id();
        }
        return get_loggedin_branch_id();
    }

    private function ensurePermission(string $module, string $can = 'is_view'): void
    {
        abort_unless(get_permission($module, $can), 403, translate('access_denied'));
    }

    // GET /settings/school
    public function school(Request $request)
    {
        $this->ensurePermission('school_settings', 'is_view');
        $branchID = $this->getBranchID($request);
        $school = Branch::find($branchID);
        return view('school_settings.school', [
            'title' => translate('school_settings'),
            'school' => $school,
            'branchID' => $branchID,
            'branches' => is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect(),
        ]);
    }

    public function schoolUpdate(Request $request)
    {
        $this->ensurePermission('school_settings', 'is_edit');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate([
            'branch_name' => ['required','string','max:255', Rule::unique('branch','name')->ignore($branchID)],
            'school_name' => ['required','string','max:255'],
            'email' => ['required','email','max:100'],
            'currency' => ['required','string','max:100'],
            'currency_symbol' => ['required','string','max:50'],
            'mobileno' => ['nullable','string','max:100'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'address' => ['nullable','string'],
            'print_text' => ['nullable','string'],
            'use_section' => ['nullable'],
        ]);

        Branch::where('id', $branchID)->update([
            'name' => $validated['branch_name'],
            'school_name' => $validated['school_name'],
            'email' => $validated['email'],
            'mobileno' => $validated['mobileno'] ?? '',
            'currency' => $validated['currency'],
            'symbol' => $validated['currency_symbol'],
            'city' => $validated['city'] ?? '',
            'state' => $validated['state'] ?? '',
            'address' => $validated['address'] ?? '',
            'use_section' => $request->has('use_section') ? 1 : 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
        }
        set_alert('success', translate('the_configuration_has_been_updated'));
        return back();
    }

    // Payment
    public function payment(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_view');
        $branchID = $this->getBranchID($request);
        $config = PaymentConfig::where('branch_id', $branchID)->first();
        return view('school_settings.payment_gateway', [
            'title' => translate('payment_control'),
            'config' => $config,
            'branch_id' => $branchID,
            'branches' => is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect(),
        ]);
    }

    private function upsertPaymentConfig(int $branchID, array $data): void
    {
        $existing = PaymentConfig::where('branch_id', $branchID)->first();
        if (!$existing) {
            // fill defaults for non-nullable columns to avoid DB constraint violations
            $defaults = ['paystack_secret_key'=>'', 'paystack_status'=>2, 'razorpay_key_id'=>'', 'razorpay_key_secret'=>'', 'razorpay_demo'=>2, 'razorpay_status'=>2];
            $data = array_merge($defaults, $data);
            $data['branch_id'] = $branchID;
            PaymentConfig::create($data);
        } else {
            $existing->update($data);
        }
    }

    public function paypalSave(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate([
            'paypal_username' => 'required|string|max:255',
            'paypal_password' => 'required|string|max:255',
            'paypal_signature' => 'required|string|max:255',
            'paypal_email' => 'required|string|max:255',
        ]);
        $validated['paypal_sandbox'] = $request->has('paypal_sandbox') ? 1 : 2;
        $this->upsertPaymentConfig($branchID, $validated);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function stripeSave(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate(['stripe_secret'=>'required|string|max:255']);
        $validated['stripe_demo'] = $request->has('stripe_demo') ? 1 : 2;
        $this->upsertPaymentConfig($branchID, $validated);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function payumoneySave(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate(['payumoney_key'=>'required|string|max:255','payumoney_salt'=>'required|string|max:255']);
        $validated['payumoney_demo'] = $request->has('payumoney_demo') ? 1 : 2;
        $this->upsertPaymentConfig($branchID, $validated);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function paystackSave(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate(['paystack_secret_key'=>'required|string|max:255']);
        $this->upsertPaymentConfig($branchID, $validated);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function razorpaySave(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate(['razorpay_key_id'=>'required|string|max:255','razorpay_key_secret'=>'required|string|max:255']);
        // razorpay_demo legacy uses checkbox but not validated earlier; keep as 2 default
        $validated['razorpay_demo'] = 2;
        $this->upsertPaymentConfig($branchID, $validated);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function paymentActive(Request $request)
    {
        $this->ensurePermission('payment_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $data = [
            'paypal_status' => $request->has('paypal_status') ? 1 : 2,
            'stripe_status' => $request->has('stripe_status') ? 1 : 2,
            'payumoney_status' => $request->has('payumoney_status') ? 1 : 2,
            'paystack_status' => $request->has('paystack_status') ? 1 : 2,
            'razorpay_status' => $request->has('razorpay_status') ? 1 : 2,
        ];
        $this->upsertPaymentConfig($branchID, $data);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    // SMS Config
    public function smsconfig(Request $request)
    {
        $this->ensurePermission('sms_settings', 'is_view');
        $branchID = $this->getBranchID($request);
        $api = [];
        $smsApis = DB::table('sms_api')->get();
        foreach ($smsApis as $row) {
            $api[$row->name] = SmsCredential::where(['sms_api_id'=>$row->id,'branch_id'=>$branchID])->first();
        }
        return view('school_settings.smsconfig', [
            'title'=>translate('sms_settings'),
            'api'=>$api,
            'branch_id'=>$branchID,
            'branches'=> is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect(),
        ]);
    }

    public function smsActive(Request $request)
    {
        $this->ensurePermission('sms_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $providerID = $request->input('sms_service_provider');
        SmsCredential::where('branch_id',$branchID)->update(['is_active'=>0]);
        $updated = SmsCredential::where(['sms_api_id'=>$providerID,'branch_id'=>$branchID])->update(['is_active'=>1]);
        $msg = $updated ? translate('information_has_been_saved_successfully') : translate('SMS configuration not found');
        return response()->json(['status'=>'success','message'=>$msg]);
    }

    private function smsUpsert(Request $request, int $apiId, array $fields)
    {
        $this->ensurePermission('sms_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $existing = SmsCredential::where(['sms_api_id'=>$apiId,'branch_id'=>$branchID])->first();
        if (!$existing) {
            $fields['sms_api_id']=$apiId; $fields['branch_id']=$branchID;
            SmsCredential::create($fields);
        } else {
            $existing->update($fields);
        }
        return response()->json(['status'=>'success','message'=>translate('information_has_been_saved_successfully')]);
    }

    public function twilio(Request $request)
    {
        $validated = $request->validate(['twilio_sid'=>'required|string','twilio_auth_token'=>'required|string','sender_number'=>'required|string']);
        return $this->smsUpsert($request, 1, ['field_one'=>$validated['twilio_sid'],'field_two'=>$validated['twilio_auth_token'],'field_three'=>$validated['sender_number']]);
    }
    public function clickatell(Request $request)
    {
        $validated = $request->validate(['clickatell_user'=>'required|string','clickatell_password'=>'required|string','clickatell_api'=>'required|string','sender_number'=>'required|string']);
        return $this->smsUpsert($request, 2, ['field_one'=>$validated['clickatell_user'],'field_two'=>$validated['clickatell_password'],'field_three'=>$validated['clickatell_api'],'field_four'=>$validated['sender_number']]);
    }
    public function msg91(Request $request)
    {
        $validated = $request->validate(['msg91_auth_key'=>'required|string','sender_id'=>'required|string']);
        return $this->smsUpsert($request, 3, ['field_one'=>$validated['msg91_auth_key'],'field_two'=>$validated['sender_id']]);
    }
    public function bulksms(Request $request)
    {
        $validated = $request->validate(['bulk_sms_username'=>'required|string','bulk_sms_password'=>'required|string']);
        return $this->smsUpsert($request, 4, ['field_one'=>$validated['bulk_sms_username'],'field_two'=>$validated['bulk_sms_password']]);
    }
    public function textlocal(Request $request)
    {
        $validated = $request->validate(['textlocal_username'=>'required|string','textlocal_sender_id'=>'required|string','textlocal_hash_key'=>'required|string']);
        return $this->smsUpsert($request, 5, ['field_one'=>$validated['textlocal_username'],'field_two'=>$validated['textlocal_sender_id'],'field_three'=>$validated['textlocal_hash_key']]);
    }

    public function smstemplate(Request $request)
    {
        $this->ensurePermission('sms_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $templatelist = SmsTemplate::all();
        return view('school_settings.smstemplate', [
            'title'=>translate('sms_settings'),
            'templatelist'=>$templatelist,
            'branch_id'=>$branchID,
        ]);
    }

    public function smsTemplateSave(Request $request)
    {
        $this->ensurePermission('sms_settings', 'is_add');
        $validated = $request->validate(['template_body'=>'required|string','template_id'=>'required|integer']);
        $branchID = $this->getBranchID($request);
        $data = [
            'notify_student'=> $request->has('notify_student') ? 1 : 0,
            'notify_parent'=> $request->has('notify_parent') ? 1 : 0,
            'template_body'=>$validated['template_body'],
            'template_id'=>$validated['template_id'],
            'branch_id'=>$branchID,
        ];
        $existing = SmsTemplateDetail::where(['template_id'=>$validated['template_id'],'branch_id'=>$branchID])->first();
        if (!$existing) SmsTemplateDetail::create($data); else $existing->update($data);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    // Email config
    public function emailconfig(Request $request)
    {
        $this->ensurePermission('email_settings', 'is_view');
        $branchID = $this->getBranchID($request);
        $config = EmailConfig::where('branch_id',$branchID)->first();
        return view('school_settings.emailconfig', [
            'title'=>translate('email_settings'),
            'config'=>$config,
            'branch_id'=>$branchID,
            'branches'=> is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect(),
        ]);
    }

    public function saveEmailConfig(Request $request)
    {
        $this->ensurePermission('email_settings', 'is_add');
        $branchID = $this->getBranchID($request);
        $validated = $request->validate([
            'email'=>'required|email|max:255',
            'protocol'=>'required|in:smtp,mail,sendmail',
            'smtp_host'=>'required_if:protocol,smtp|nullable|string|max:255',
            'smtp_user'=>'required_if:protocol,smtp|nullable|string|max:255',
            'smtp_pass'=>'required_if:protocol,smtp|nullable|string|max:255',
            'smtp_port'=>'required_if:protocol,smtp|nullable|string|max:100',
            'smtp_encryption'=>'required_if:protocol,smtp|nullable|string|max:10',
        ]);
        $data = [
            'email'=>$validated['email'],
            'protocol'=>$validated['protocol'],
            'branch_id'=>$branchID,
        ];
        if ($validated['protocol']==='smtp') {
            $data['smtp_host']=$validated['smtp_host'];
            $data['smtp_user']=$validated['smtp_user'];
            $data['smtp_pass']=$validated['smtp_pass'];
            $data['smtp_port']=$validated['smtp_port'];
            $data['smtp_encryption']=$validated['smtp_encryption'];
        }
        $existing = EmailConfig::where('branch_id',$branchID)->first();
        if (!$existing) EmailConfig::create($data); else $existing->update($data);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    public function emailtemplate(Request $request)
    {
        $this->ensurePermission('email_settings', 'is_view');
        $branchID = $this->getBranchID($request);
        $templatelist = EmailTemplate::all();
        return view('school_settings.emailtemplate', [
            'title'=>translate('email_settings'),
            'templatelist'=>$templatelist,
            'branch_id'=>$branchID,
        ]);
    }

    public function emailTemplateSave(Request $request)
    {
        $this->ensurePermission('email_settings', 'is_add');
        $validated = $request->validate(['subject'=>'required|string|max:255','template_body'=>'required|string','template_id'=>'required|integer']);
        $branchID = $this->getBranchID($request);
        $data = [
            'template_id'=>$validated['template_id'],
            'subject'=>$validated['subject'],
            'template_body'=>$validated['template_body'],
            'notified'=> $request->has('notify_enable') ? 1 : 0,
            'branch_id'=>$branchID,
        ];
        $existing = EmailTemplateDetail::where(['template_id'=>$validated['template_id'],'branch_id'=>$branchID])->first();
        if (!$existing) EmailTemplateDetail::create($data); else $existing->update($data);
        return response()->json(['status'=>'success','message'=>translate('the_configuration_has_been_updated')]);
    }

    // Accounting links
    public function accountingLinks(Request $request)
    {
        $this->ensurePermission('accounting_links', 'is_view');
        $branchID = $this->getBranchID($request);
        $transactions = TransactionLink::where('branch_id',$branchID)->first();
        return view('school_settings.accounting_links', [
            'title'=>translate('accounting_links'),
            'transactions'=>$transactions,
            'branch_id'=>$branchID,
        ]);
    }

    public function accountingLinksSave(Request $request)
    {
        $this->ensurePermission('accounting_links', 'is_edit');
        $branchID = $this->getBranchID($request);
        $data = ['branch_id'=>$branchID];
        if ($request->has('status')) {
            $request->validate(['deposit'=>'required','expense'=>'required']);
            $data['status']=1;
            $data['deposit']=$request->input('deposit');
            $data['expense']=$request->input('expense');
        } else {
            $data['status']=0;
        }
        $existing = TransactionLink::where('branch_id',$branchID)->first();
        if ($existing) $existing->update($data); else TransactionLink::create($data);
        return response()->json(['status'=>'success','message'=>translate('information_has_been_saved_successfully')]);
    }

    // Live class config
    public function liveClassConfig(Request $request)
    {
        $this->ensurePermission('live_class_config', 'is_view');
        $branchID = $this->getBranchID($request);
        $config = LiveClassConfig::where('branch_id',$branchID)->first();
        return view('school_settings.live_class_config', [
            'title'=>translate('live_class').' '.translate('settings'),
            'config'=>$config,
            'branch_id'=>$branchID,
        ]);
    }

    public function liveClassSave(Request $request)
    {
        $this->ensurePermission('live_class_config', 'is_edit');
        $validated = $request->validate(['zoom_api_key'=>'required|string|max:255','zoom_api_secret'=>'required|string|max:255']);
        $branchID = $this->getBranchID($request);
        $existing = LiveClassConfig::where('branch_id',$branchID)->first();
        if ($existing) $existing->update(['zoom_api_key'=>$validated['zoom_api_key'],'zoom_api_secret'=>$validated['zoom_api_secret']]);
        else LiveClassConfig::create(['zoom_api_key'=>$validated['zoom_api_key'],'zoom_api_secret'=>$validated['zoom_api_secret'],'branch_id'=>$branchID]);
        return response()->json(['status'=>'success','message'=>translate('information_has_been_saved_successfully')]);
    }
}
