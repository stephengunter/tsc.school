@component('mail::message')
{{ $username }} ，您好！


我們已為您啟動密碼重設程序，請點擊以下連結重新設定您的密碼：


@component('mail::button', ['url' => $url])
點此重新設定您的密碼
@endcomponent



@endcomponent
