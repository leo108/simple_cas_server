@lang('passwords.email_content') <a href="{{ $link = route('reset_pwd_page', ['token' => $token]).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
