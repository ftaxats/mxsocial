<?php
  
namespace App\Enums;

use App\Enums\EnumTrait;
use Illuminate\Support\Arr;

enum NotificationTemplateEnum :string {



    use EnumTrait;

    case PASSWORD_RESET                 = 'PASSWORD_RESET';
    case REGISTRATION_VERIFY            = 'REGISTRATION_VERIFY';
    case SUPPORT_TICKET_REPLY           = 'SUPPORT_TICKET_REPLY';
    case TEST_MAIL                      = 'TEST_MAIL';
    case TICKET_REPLY                   = 'TICKET_REPLY';
    case OTP_VERIFY                     = 'OTP_VERIFY';
    case WITHDRAWAL_REQUEST_ACCEPTED    = 'WITHDRAWAL_REQUEST_ACCEPTED';
    case WITHDRAWAL_REQUEST_SUBMIT      = 'WITHDRAWAL_REQUEST_SUBMIT';
    case DEPOSIT_REQUEST                = 'DEPOSIT_REQUEST';
    case DEPOSIT_REQUEST_ACCEPTED       = 'DEPOSIT_REQUEST_ACCEPTED';
    case NEW_DEPOSIT                    = 'NEW_DEPOSIT';
    case WITHDRAWAL_REQUEST_REJECTED    = 'WITHDRAWAL_REQUEST_REJECTED';
    case WITHDRAWAL_REQUEST_RECEIVED    = 'WITHDRAWAL_REQUEST_RECEIVED';
    case NEW_TICKET                     = 'NEW_TICKET';
    case DEPOSIT_REQUEST_REJECTED       = 'DEPOSIT_REQUEST_REJECTED';
    case USER_ACTION                    = 'USER_ACTION';
    case KYC_UPDATE                     = 'KYC_UPDATE';
    case KYC_APPLIED                    = 'KYC_APPLIED';
    case SUBSCRIPTION_CREATED           = 'SUBSCRIPTION_CREATED';
    case SUBSCRIPTION_STATUS            = 'SUBSCRIPTION_STATUS';
    case SUBSCRIPTION_FAILED            = 'SUBSCRIPTION_FAILED';
    case SUBSCRIPTION_EXPIRED           = 'SUBSCRIPTION_EXPIRED';
    case CONTACT_REPLY                  = 'CONTACT_REPLY';





    /**
     * Get Notification Template
     *
     * @return array
     */
    public static function notificationTemplateEnum(? string $template =  null) : array {


       $templates =   [
                       self::PASSWORD_RESET->value => [
                                "name"      => k2t(self::PASSWORD_RESET->value),
                                "subject"   => "Password Reset",
                                "body"      => "We have received a request to reset the password for your account on {{otp_code}} and Request time {{time}}",
                                "sms_body"  => "We have received a request to reset the password for your account on {{otp_code}} and Request time {{time}}",
                                "template_key" => [
                                    'otp_code' => "Password Reset Code",
                                    'time'     => "Password Reset Time",
                                ]
                        ],

                        self::REGISTRATION_VERIFY->value => [
                            "name"      => k2t(self::REGISTRATION_VERIFY->value),
                            "subject"   => "Registration Verify",
                            "body"      => "We have received a request to create an account, you need to verify email first, your verification code is {{otp_code}} and request time {{time}}",
                            "sms_body"  => "We have received a request to create an account, you need to verify email first, your verification code is {{otp_code}} and request time {{time}}",
                            "template_key" => ([
                                'otp_code'  => "Verification Code",
                                'time' => "Time",
                            ])
                        ],
                        
                        self::SUPPORT_TICKET_REPLY->value => [
                            "name"      => k2t(self::SUPPORT_TICKET_REPLY->value),
                            "subject"   => "Support Ticket",
                            "body"      => "<p>Hello Dear ! To provide a response to Ticket ID {{ticket_number}}, kindly click the link provided below in order to reply to the ticket &nbsp;<a style=\"background-color:#13C56B;border-radius:4px;color:#fff;display:inline-flex;font-weight:400;line-height:1;padding:5px 10px;text-align:center:font-size:14px;text-decoration:none;\" href=\"{{link}}\">Link</a></p>",
                            "sms_body"  => "Hello Dear ! To get a response to Ticket ID {{ticket_number}}, kindly click the link provided below in order to reply to the ticket. {{link}}",
                            "template_key" => ([
                                'ticket_number' => "Support Ticket Number",
                                'link'          => "Ticket URL For relpy",
                            ])
                        ],



                        self::TEST_MAIL->value => [
                            "name"      => k2t(self::TEST_MAIL->value),
                            "subject"   => "Test Mail",
                            "body"      => "This is testing mail for mail configuration Request time<span style=\"background-color: rgb(255, 255, 0);\"> {{time}}</span></h5>",
                            "sms_body"  => "",
                            "template_key" => ([
                                'time' => "Time",
                            ])
                        ],

                        self::TICKET_REPLY->value => [
                            "name"      => k2t(self::TICKET_REPLY->value),
                            "subject"   => "Support Ticket Reply",
                            "body"      => "{{name}}!! Just Replied To A Ticket..  To provide a response to Ticket ID {{ticket_number}}, kindly click the link provided below in order to reply to the ticket.  {{link}}",
                            "sms_body"  => "{{name}}!! Just Replied To A Ticket..  To provide a response to Ticket ID {{ticket_number}}, kindly click the link provided below in order to reply to the ticket.  {{link}}",
                            "template_key" => ([
                                
                                'name'          => "Admin/Agent/User Name",
                                'ticket_number' => "Support Ticket Number",
                                'link'          => "Ticket URL For relpy"
                            ])
                        ],


                        self::OTP_VERIFY->value => [
                            "name"      => k2t(self::OTP_VERIFY->value),
                            "subject"   => "OTP Verificaton",
                            "body"      => "Your Otp {{otp_code}} and request time {{time}}, expired time {{expire_time}}",
                            "sms_body"  => "Your Otp {{otp_code}} and request time {{time}}, expired time {{expire_time}}",
                            "template_key" => ([
                                'otp_code'         => "OTP (One time password)",
                                'time'        => "Time",
                                'expire_time' => "OTP Expired Time"
                            ])
                        ],

                        self::WITHDRAWAL_REQUEST_ACCEPTED->value => [
                            "name"      => k2t(self::WITHDRAWAL_REQUEST_ACCEPTED->value),
                            "subject"   => "Withdrawal Request Accepted",
                            "body"      => "We are pleased to inform you that your withdrawal request has been accepted. Here are the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Time of Approval: {{time}} The funds will be processed accordingly.",
                            "sms_body"  => "We are pleased to inform you that your withdrawal request has been accepted. Here are the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Time of Approval: {{time}} The funds will be processed accordingly.",
                            "template_key" => ([
                                'time'     => "Time",
                                'trx_code' => "Transaction id",
                                'amount'   => "Withdraw amount",
                                'method'   => "Withdraw method",
                            ])
                        ],

                        self::WITHDRAWAL_REQUEST_SUBMIT->value => [
                            "name"      => k2t(self::WITHDRAWAL_REQUEST_SUBMIT->value),
                            "subject"   => "New Withdrawal Request Submitted",
                            "body"      => "A new withdrawal request has been submitted. Here are the details: User: {{name}} Transaction ID: {{trx_code}} Amount: {{amount}} Withdrawal Method: {{method}} Requested On: {{time}}",
                            "sms_body"  => "A new withdrawal request has been submitted. Here are the details: User: {{name}} Transaction ID: {{trx_code}} Amount: {{amount}} Withdrawal Method: {{method}} Requested On: {{time}}",
                            "template_key" => ([
                                'name'     => "User name",
                                'trx_code' => "Transaction id",
                                'amount'   => "Withdraw amount",
                                'method'   => "Withdraw method",
                            ])
                        ],

                        self::DEPOSIT_REQUEST->value => [
                            "name"      => k2t(self::DEPOSIT_REQUEST->value),
                            "subject"   => "New Deposit Request",
                            "body"      => "We have received your deposit request for an amount of {{amount}} via {{payment_method}} at {{time}} Your transaction code is {{trx_code}}. Please wait for our confirmation",
                            "sms_body"  => "We have received your deposit request for an amount of {{amount}} via {{payment_method}} at {{time}} Your transaction code is {{trx_code}}. Please wait for our confirmation",
                            "template_key" => ([
                                'time'           => "Time",
                                'trx_code'       => "Transaction id",
                                'amount'         => "Deposited amount",
                                'payment_method' => "Payment method",
                            ])
                        ],


                        self::DEPOSIT_REQUEST_ACCEPTED->value => [
                            "name"      => k2t(self::DEPOSIT_REQUEST_ACCEPTED->value),
                            "subject"   => "Deposit Request Accepted",
                            "body"      => "We are pleased to inform you that your deposit request has been accepted. Your transaction code is {{trx_code}}. The deposited amount is {{amount}} via {{payment_method}}",
                            "sms_body"  => "We are pleased to inform you that your deposit request has been accepted. Your transaction code is {{trx_code}}. The deposited amount is {{amount}} via {{payment_method}}",
                            
                            "template_key" => ([
                                'trx_code'       => "Transaction id",
                                'amount'         => "Deposited amount",
                                'payment_method' => "Payment method",
                            ])
                        ],



                        self::NEW_DEPOSIT->value => [
                            "name"      => k2t(self::NEW_DEPOSIT->value),
                            "subject"   => "Newly Deposited Amount",
                            "body"      => "A new deposit has been made by {{name}}. Here are the details: - User: {{name}} - Transaction Code: {{trx_code}} - Amount: {{amount}} - Payment Method: {{payment_method}} - Time of Deposit: {{time}} Please review and take the necessary actions.",
                            "sms_body"  => "A new deposit has been made by {{name}}. Here are the details: - User: {{name}} - Transaction Code: {{trx_code}} - Amount: {{amount}} - Payment Method: {{payment_method}} - Time of Deposit: {{time}} Please review and take the necessary actions.",
                            "template_key" => ([
                                'time'           => "Time",
                                'trx_code'       => "Transaction id",
                                'amount'         => "Deposited amount",
                                'payment_method' => "Payment method",
                                'name'           => "User name"
                            ])
                        ],


                        self::WITHDRAWAL_REQUEST_REJECTED->value => [
                            "name"      => k2t(self::WITHDRAWAL_REQUEST_REJECTED->value),
                            "subject"   => "Withdrawal Request Rejected",
                            "body"      => "We regret to inform you that your withdrawal request has been rejected. Please review the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Reason for Rejection: {{reason}} - Time of Rejection: {{time}}",
                            "sms_body"  => "We regret to inform you that your withdrawal request has been rejected. Please review the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Reason for Rejection: {{reason}} - Time of Rejection: {{time}}",
                            "template_key" => ([
                                'time'     => "Time",
                                'trx_code' => "Transaction id",
                                'amount'   => "Withdraw amount",
                                'method'   => "Withdraw method",
                                'reason'   => "Rejection reason"
                            ])
                        ],


                        self::WITHDRAWAL_REQUEST_RECEIVED->value => [
                            "name"      => k2t(self::WITHDRAWAL_REQUEST_RECEIVED->value),
                            "subject"   => "Withdrawal Request Received",
                            "body"      => "We have received your withdrawal request. Here are the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Time : {{time}} Your request is currently being processed. We will notify you once the status is updated.",
                            "sms_body"      => "We have received your withdrawal request. Here are the details: - Transaction Code: {{trx_code}} - Amount: {{amount}} - Method: {{method}} - Time : {{time}} Your request is currently being processed. We will notify you once the status is updated.",
                            "template_key" => ([
                                'time'     => "Time",
                                'trx_code' => "Transaction id",
                                'amount'   => "Withdraw amount",
                                'method'   => "Withdraw method"
                            ])
                        ],

                        self::NEW_TICKET->value => [
                            "name"      => k2t(self::NEW_TICKET->value),
                            "subject"   => "New Ticket",
                            "body"      => "A new ticket has been created with the following details: Ticket ID: {{ticket_number}} Created by: {{name}} Date and Time: {{time}} Priority: {{priority}}",
                            "sms_body"  => "A new ticket has been created with the following details: Ticket ID: {{ticket_number}} Created by: {{name}} Date and Time: {{time}} Priority: {{priority}}",
                            "template_key" => ([
                                'ticket_number' => "Support Ticket Number",
                                'name'          => "User name",
                                'time'          => "Created Date and time",
                                'priority'      => "Ticket Priority"
                            ])
                        ],
                        self::DEPOSIT_REQUEST_REJECTED->value => [
                            "name"      => k2t(self::DEPOSIT_REQUEST_REJECTED->value),
                            "subject"   => "Deposit Request Rejected",
                            "body"      => "We regret to inform you that your deposit request has been rejected. reason : {{reason}} Your transaction code is {{trx_code}}. The deposited amount is {{amount}} via {{payment_method}}",
                            "sms_body"  => "We regret to inform you that your deposit request has been rejected. reason : {{reason}} Your transaction code is {{trx_code}}. The deposited amount is {{amount}} via {{payment_method}}",
                            "template_key" => ([
                                'trx_code'       => "Transaction id",
                                'amount'         => "Deposited amount",
                                'payment_method' => "Payment method",
                                'reason'         => "Rejection reason"
                            ])
                        ],
                        self::USER_ACTION->value => [
                            "name"      => k2t(self::USER_ACTION->value),
                            "subject"   => "New User Action",
                            "body"      => "A new {{type}}  has occurred. Here are the details: {{details}} Please respond promptly.",
                            "sms_body"  => "A new {{type}}  has occurred. Here are the details: {{details}} Please respond promptly.",
                            "template_key" => ([
                                'type'       => "Action type",
                                'details'    => "Action Details"
                            ])
                        ],


                        self::KYC_UPDATE->value => [
                            "name"      => k2t(self::KYC_UPDATE->value),
                            "subject"   => "KYC Log Status Updated",
                            "body"      => "We're here to inform you that there has been an update to your KYC (Know Your Customer) log status.
                                            Kyc Information:
                                                Applied By : {{name}}
                                                status     : {{status}}",

                            "sms_body"  => "We're here to inform you that there has been an update to your KYC (Know Your Customer) log status.
                                            Kyc Information:Applied By : {{name}} status : {{status}}",
                            "template_key" => ([
                                'name'       => "User name",
                                'status'     => "Verification status"
                            ])
                        ],


                        self::KYC_APPLIED->value => [
                            "name"      => k2t(self::KYC_APPLIED->value),
                            "subject"   => "New KYC Verification Application Received",
                            "body"      => "A new user has applied for KYC (Know Your Customer) verification. Here are the details
                            Kyc Information:Applied By :{{name}} Application time :{{time}}",
                            "sms_body"  => "A new user has applied for KYC (Know Your Customer) verification. Here are the details
                            Kyc Information:Applied By :{{name}} Application time :{{time}}",
                            "template_key" => ([
                                'name'       => "User name",
                                'time'     => "Time"
                            ])
                        ],

                        /** new template */
                        self::SUBSCRIPTION_CREATED->value => [
                            "name"      => k2t(self::SUBSCRIPTION_CREATED->value),
                            "subject"   => "New Subscription Created",
                            "body"      => "A new subscription has been created.
                                              Subscription Details:
                                            - User: {{name}}
                                            - Subscription Plan: {{package_name}}
                                            - Start Date: {{start_date}}
                                            - End Date: {{end_date}",
                            "sms_body"  => "A new subscription has been created.
                                              Subscription Details:
                                            - User: {{name}}
                                            - Subscription Plan: {{package_name}}
                                            - Start Date: {{start_date}}
                                            - End Date: {{end_date}}",
                            "template_key" => ([
                                'name'           => "User name",
                                'start_date'     => "Start Date",
                                'end_date'       => "End Date",
                                'package_name'   => "Package name",
                            ])
                        ],

                        self::SUBSCRIPTION_STATUS->value => [
                            "name"      => k2t(self::SUBSCRIPTION_STATUS->value),
                            "subject"   => "Subscription Status Updated",
                            "body"      => "We wanted to inform you that the status of your subscription has been updated.
                                                Subscription Details:
                                                - Plan: {{plan_name}}
                                                - Status: {{status}}
                                                - Time :{{time}}",
                            "sms_body"  => "We wanted to inform you that the status of your subscription has been updated.
                                                Subscription Details:
                                                - Plan: {{plan_name}}
                                                - Status: {{status}}
                                                - Time :{{time}}",
                            "template_key" => ([
                                'status'         => "Status",
                                'time'           => "Time",
                                'plan_name'      => "Package name"
                            ])
                        ],


                        self::SUBSCRIPTION_FAILED->value => [
                            "name"      => k2t(self::SUBSCRIPTION_FAILED->value),
                            "subject"   => "Auto Subscription Renewal Failed",
                            "body"      => "We regret to inform you that the automatic renewal of your subscription has failed. 
                                                Subscription Details:
                                                - Plan: {{name}}
                                                - Reason: {{reason}}
                                                - Time :{{time}}",
                            "sms_body"  => "We regret to inform you that the automatic renewal of your subscription has failed. 
                                                Subscription Details:
                                                - Plan: {{name}}
                                                - Reason: {{reason}}
                                                - Time :{{time}}",
                            "template_key" => ([
                                'reason'         => "Failed Reason",
                                'time'           => "Time",
                                'name'           => "Package name"
                            ])
                        ],
                        self::SUBSCRIPTION_EXPIRED->value => [
                            "name"      => k2t(self::SUBSCRIPTION_EXPIRED->value),
                            "subject"   => "Subscription Expired",
                            "body"      => "Your {{name}} Package Subscription Has Been Expired!! at time {{time}}",
                            "sms_body"  => "Your {{name}} Package Subscription Has Been Expired!! at time {{time}}",
                            "template_key" => ([
                                'time'           => "Time",
                                'name'           => "Package name"
                            ])
                        ],
                        self::CONTACT_REPLY->value => [
                            "name"      => k2t(self::CONTACT_REPLY->value),
                            "subject"   => "Contact Message reply",
                            "body"      => "Hello Dear! {{email}} {{message}}",
                            "sms_body"  => "Hello Dear! {{email}} {{message}}",
                            "template_key" => ([
                                'email'           => "email",
                                'message'           => "message"
                            ])
                        ],
                       
                ];

       return $template 
                  ? Arr::get($templates , $template , [])
                  : $templates ;
      

    }
}