<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LEHSFF Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:        #142552;
            --navy-deep:   #0d1c3e;
            --navy-mid:    #1c3068;
            --green:       #05923B;
            --green-light: #07b548;
            --green-pale:  #e6f7ed;
            --white:       #ffffff;
            --off-white:   #f5f7fb;
            --border:      #e4e9f2;
            --smoke:       #6b7280;
            --charcoal:    #111827;
            --display:     'Syne', sans-serif;
            --body:        'Plus Jakarta Sans', sans-serif;
        }

        html, body { height: 100%; }

        body {
            font-family: var(--body);
            background: var(--off-white);
            color: var(--charcoal);
            display: flex;
            min-height: 100vh;
        }

        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 9999;
        }

        /* ══════════════════════════
           LEFT PANEL
        ══════════════════════════ */
        .info-panel {
            width: 56%;
            background: var(--white);
            border-right: 1px solid var(--border);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem 4.5rem;
            overflow: hidden;
        }
        .bg-blob-1 {
            position: absolute; top: -180px; right: -180px;
            width: 520px; height: 520px; border-radius: 50%;
            background: radial-gradient(circle, rgba(20,37,82,.05) 0%, transparent 70%);
            pointer-events: none;
        }
        .bg-blob-2 {
            position: absolute; bottom: -220px; left: -120px;
            width: 480px; height: 480px; border-radius: 50%;
            background: radial-gradient(circle, rgba(5,146,59,.05) 0%, transparent 65%);
            pointer-events: none;
        }
        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(20,37,82,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(20,37,82,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        .panel-brand {
            position: relative; z-index: 1;
            animation: fadeUp .5s ease .05s both;
        }
        .brand-logo {
            height: 56px; width: auto; object-fit: contain; display: block;
        }
        .panel-hero {
            position: relative; z-index: 1; flex: 1;
            display: flex; flex-direction: column; justify-content: center;
            padding: 2rem 0;
        }
        .panel-eyebrow {
            display: inline-flex; align-items: center; gap: .55rem;
            font-family: var(--body); font-size: .65rem; font-weight: 600;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--green); margin-bottom: 1.4rem;
            animation: fadeUp .5s ease .12s both;
        }
        .panel-eyebrow .ey-dot {
            display: block; width: 6px; height: 6px; border-radius: 50%;
            background: var(--green); flex-shrink: 0;
        }
        .panel-headline {
            font-family: var(--display);
            font-size: clamp(2.7rem, 4.2vw, 4.1rem);
            font-weight: 800; line-height: 1.04;
            color: var(--navy); letter-spacing: -.025em;
            margin-bottom: 1.5rem;
            animation: fadeUp .6s ease .2s both;
        }
        .panel-headline .hl-green {
            color: var(--green);
            position: relative; display: inline-block;
        }
        .panel-headline .hl-green::after {
            content: '';
            position: absolute; left: 0; bottom: -4px;
            width: 100%; height: 3px;
            background: var(--green); border-radius: 2px; opacity: .35;
        }
        .panel-desc {
            font-size: .93rem; font-weight: 300;
            color: var(--smoke); line-height: 1.8;
            max-width: 430px; margin-bottom: 2.5rem;
            animation: fadeUp .6s ease .28s both;
        }
        .panel-features {
            display: flex; flex-wrap: wrap; gap: .4rem;
            animation: fadeUp .6s ease .35s both;
        }
        .feat-pill {
            display: inline-flex; align-items: center; gap: .38rem;
            padding: .28rem .75rem;
            background: var(--off-white); border: 1px solid var(--border);
            border-radius: 50px;
            font-family: var(--body); font-size: .7rem; font-weight: 400;
            color: var(--smoke); transition: all .2s;
        }
        .feat-pill:hover {
            background: var(--green-pale);
            border-color: rgba(5,146,59,.3); color: var(--green);
        }
        .pill-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--green); opacity: .8; flex-shrink: 0;
        }
        .panel-stats {
            position: relative; z-index: 1;
            display: flex; gap: 0;
            padding-top: 2rem; border-top: 1px solid var(--border);
            animation: fadeUp .6s ease .42s both;
        }
        .stat {
            flex: 1; padding-right: 1.5rem;
            border-right: 1px solid var(--border); margin-right: 1.5rem;
        }
        .stat:last-child { border-right: none; margin-right: 0; }
        .stat-num {
            font-family: var(--display); font-size: 2rem; font-weight: 800;
            color: var(--navy); line-height: 1; letter-spacing: -.02em;
        }
        .stat-label {
            font-family: var(--body); font-size: .63rem; font-weight: 400;
            letter-spacing: .08em; text-transform: uppercase;
            color: var(--smoke); opacity: .6; margin-top: .25rem;
        }

        /* ══════════════════════════
           RIGHT PANEL
        ══════════════════════════ */
        .form-panel {
            width: 44%;
            background: var(--off-white);
            display: flex; flex-direction: column;
            justify-content: center;
            padding: 4rem 3.5rem;
            position: relative;
            overflow: hidden;
        }
        .form-panel::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--navy) 0%, var(--green) 100%);
        }
        .form-panel::after {
            content: '';
            position: absolute; bottom: 0; right: 0;
            width: 120px; height: 120px;
            background: radial-gradient(circle at bottom right, rgba(5,146,59,.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── FORM VIEWS ── */
        /* Both forms sit in the same space; we slide between them */
        .forms-wrapper {
            position: relative;
            overflow: hidden;
        }

        .form-view {
            width: 100%;
            transition: opacity .35s ease, transform .35s ease;
        }
        /* hidden state */
        .form-view.hidden {
            opacity: 0;
            pointer-events: none;
            position: absolute;
            top: 0; left: 0;
            transform: translateX(40px);
        }
        /* register hidden goes left */
        #register-view.hidden {
            transform: translateX(-40px);
        }
        /* visible state */
        .form-view.visible {
            opacity: 1;
            pointer-events: all;
            position: relative;
            transform: translateX(0);
        }

        /* ── SHARED FORM STYLES ── */
        .form-header { margin-bottom: 2rem; }
        .form-eyebrow {
            display: inline-flex; align-items: center; gap: .4rem;
            font-family: var(--body); font-size: .6rem; font-weight: 600;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--green); margin-bottom: .6rem;
        }
        .form-eyebrow::before {
            content: '';
            display: block; width: 18px; height: 2px;
            background: var(--green); border-radius: 1px;
        }
        .form-title {
            font-family: var(--display); font-size: 1.85rem; font-weight: 700;
            color: var(--navy); line-height: 1.1; letter-spacing: -.02em;
        }
        .form-sub {
            font-size: .82rem; font-weight: 400;
            color: var(--smoke); margin-top: .4rem; line-height: 1.6;
        }
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-family: var(--body); font-size: .7rem; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            color: var(--navy); margin-bottom: .38rem; opacity: .75;
        }
        .field input {
            display: block; width: 100%;
            padding: .7rem 1rem;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: var(--body); font-size: .88rem; font-weight: 400;
            color: var(--charcoal); outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(5,146,59,.1);
        }
        .field input::placeholder { color: rgba(107,114,128,.4); }

        /* two-col row for register form */
        .field-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: .75rem;
        }

        .field-aux {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.2rem;
        }
        .check-wrap {
            display: flex; align-items: center; gap: .45rem;
            font-size: .78rem; font-weight: 400; color: var(--smoke); cursor: pointer;
        }
        .check-wrap input[type="checkbox"] {
            width: 15px; height: 15px; accent-color: var(--green); cursor: pointer;
        }
        .forgot-link {
            font-size: .78rem; font-weight: 600;
            color: var(--navy); text-decoration: none; opacity: .65;
            border-bottom: 1px solid transparent;
            transition: opacity .2s, border-color .2s;
        }
        .forgot-link:hover { opacity: 1; border-bottom-color: var(--navy); }

        /* Buttons */
        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            width: 100%; padding: .88rem;
            background: var(--navy);
            border: none; border-radius: 50px;
            color: var(--white);
            font-family: var(--display); font-size: .83rem; font-weight: 700;
            letter-spacing: .04em; text-transform: uppercase;
            cursor: pointer; text-decoration: none;
            box-shadow: 0 4px 16px rgba(20,37,82,.25);
            transition: all .22s;
        }
        .btn-primary:hover {
            background: var(--navy-mid);
            box-shadow: 0 6px 20px rgba(20,37,82,.35);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary svg { transition: transform .2s; }
        .btn-primary:hover svg { transform: translateX(3px); }

        .btn-secondary {
            display: flex; align-items: center; justify-content: center; gap: .45rem;
            width: 100%; padding: .82rem;
            background: var(--green-pale);
            border: 1.5px solid rgba(5,146,59,.2);
            border-radius: 50px;
            color: var(--green);
            font-family: var(--display); font-size: .83rem; font-weight: 700;
            letter-spacing: .04em; text-transform: uppercase;
            text-decoration: none; cursor: pointer;
            transition: all .22s;
        }
        .btn-secondary:hover {
            background: var(--green); border-color: var(--green);
            color: var(--white);
            box-shadow: 0 4px 16px rgba(5,146,59,.3);
            transform: translateY(-1px);
        }

        .btn-green {
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            width: 100%; padding: .88rem;
            background: var(--green);
            border: none; border-radius: 50px;
            color: var(--white);
            font-family: var(--display); font-size: .83rem; font-weight: 700;
            letter-spacing: .04em; text-transform: uppercase;
            cursor: pointer; text-decoration: none;
            box-shadow: 0 4px 16px rgba(5,146,59,.28);
            transition: all .22s;
        }
        .btn-green:hover {
            background: var(--green-light);
            box-shadow: 0 6px 20px rgba(5,146,59,.38);
            transform: translateY(-1px);
        }
        .btn-green:active { transform: translateY(0); }
        .btn-green svg { transition: transform .2s; }
        .btn-green:hover svg { transform: translateX(3px); }

        .divider {
            display: flex; align-items: center; gap: .6rem;
            margin: 1.1rem 0;
            font-family: var(--body); font-size: .65rem; font-weight: 400;
            letter-spacing: .08em; text-transform: uppercase;
            color: rgba(107,114,128,.4);
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* Switch link */
        .switch-link {
            margin-top: 1.4rem; text-align: center;
        }
        .switch-link p {
            font-size: .78rem; color: var(--smoke);
        }
        .switch-link button {
            background: none; border: none; cursor: pointer;
            font-family: var(--body); font-size: .78rem; font-weight: 600;
            color: var(--navy); text-decoration: underline; text-underline-offset: 2px;
            transition: color .2s;
            padding: 0;
        }
        .switch-link button:hover { color: var(--green); }

        /* Role note */
        .role-note {
            margin-top: 1.5rem;
            padding: .8rem 1rem;
            background: var(--white);
            border-radius: 14px; border: 1px solid var(--border);
        }
        .role-note-label {
            font-family: var(--body); font-size: .58rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: var(--navy); opacity: .5; margin-bottom: .4rem;
        }
        .role-chips { display: flex; flex-wrap: wrap; gap: .3rem; }
        .role-chip {
            font-family: var(--body); font-size: .67rem; font-weight: 500;
            padding: .18rem .6rem;
            background: var(--off-white); border: 1px solid var(--border);
            color: var(--navy); border-radius: 50px;
        }

        /* Footer */
        .form-footer {
            margin-top: 1.4rem; padding-top: 1.1rem;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .form-footer p {
            font-size: .66rem; color: rgba(107,114,128,.5);
            line-height: 1.65;
        }
        .form-footer a {
            color: var(--navy); opacity: .6;
            text-decoration: underline; text-underline-offset: 2px;
            transition: opacity .2s;
        }
        .form-footer a:hover { opacity: 1; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .info-panel .panel-brand   { animation: fadeUp .5s ease .05s both; }
        .info-panel .panel-eyebrow { animation: fadeUp .5s ease .12s both; }
        .info-panel .panel-headline{ animation: fadeUp .6s ease .2s both; }
        .info-panel .panel-desc    { animation: fadeUp .6s ease .28s both; }
        .info-panel .panel-features{ animation: fadeUp .6s ease .35s both; }
        .info-panel .panel-stats   { animation: fadeUp .6s ease .42s both; }

        /* ── RESPONSIVE ── */
        @media (max-width: 920px) {
            body { flex-direction: column; }
            .info-panel, .form-panel { width: 100%; }
            .info-panel { padding: 3rem 2rem; min-height: auto; border-right: none; border-bottom: 1px solid var(--border); }
            .form-panel { padding: 3rem 2rem; }
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ════════ LEFT ════════ -->
    <div class="info-panel">
        <div class="bg-blob-1"></div>
        <div class="bg-blob-2"></div>
        <div class="bg-grid"></div>

        <div class="panel-brand">
            <img src="{{ asset('images/logo.png') }}" alt="LEHSFF Logo" class="brand-logo">
        </div>

        <div class="panel-hero">
            <div class="panel-eyebrow">
                <span class="ey-dot"></span>
                Entrepreneurship Hub &amp; Seed Financing Facility
            </div>
            <h1 class="panel-headline">
                Empowering<br>
                <span class="hl-green">Lesotho's</span><br>
                Entrepreneurs
            </h1>
            <p class="panel-desc">
                A centralised platform connecting entrepreneurs, mentors, investors, and enterprise support organisations to strengthen Lesotho's entrepreneurial ecosystem.
            </p>
            <div class="panel-features">
                <span class="feat-pill"><span class="pill-dot"></span>Incubation Management</span>
                <span class="feat-pill"><span class="pill-dot"></span>ESO Reporting</span>
            </div>
        </div>
    </div>


    <!-- ════════ RIGHT ════════ -->
    <div class="form-panel">
        <div class="forms-wrapper">

            <!-- ── LOGIN VIEW ── -->
            <div class="form-view visible" id="login-view">
                <div class="form-header">
                    <div class="form-eyebrow">Secure Portal Access</div>
                    <h2 class="form-title">Welcome back</h2>
                    <p class="form-sub">Sign in to access your dashboard, reports, and ecosystem tools.</p>
                </div>

                @if (Route::has('login'))
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field">
                        <label for="login_email">Email Address</label>
                        <input id="login_email" type="email" name="email"
                            placeholder="you@example.com"
                            value="{{ old('email') }}"
                            required autofocus autocomplete="email">
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                    </div>
                    <div class="field">
                        <label for="login_password">Password</label>
                        <input id="login_password" type="password" name="password"
                            placeholder="••••••••••"
                            required autocomplete="current-password"> 
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                    </div>
                    <div class="field-aux">
                        <label class="check-wrap">
                            <input type="checkbox" name="remember">
                            Keep me signed in
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>
                    <button type="submit" class="btn-primary">
                        Sign In
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M2.5 7.5H12.5M9 4L12.5 7.5L9 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
                @endif

                <button type="button" class="btn-secondary mt-3" onclick="showRegister()">
                    Create New Account
                </button>

                <div class="role-note">
                    <div class="role-note-label">Portal Access For</div>
                    <div class="role-chips">
                        <span class="role-chip">Entrepreneurs</span>
                        <span class="role-chip">Mentors</span>
                        <span class="role-chip">Investors</span>
                        <span class="role-chip">ESOs</span>
                        <span class="role-chip">CAFI Admins</span>
                    </div>
                </div>

                <div class="form-footer">
                    <p>
                        Operated under the CAFI Project, Ministry of Trade,<br>
                        Industry, Business Development &amp; Tourism · Lesotho.<br>
                        <a href="#">Privacy Policy</a> &nbsp;·&nbsp; <a href="#">Contact Support</a>
                    </p>
                </div>
            </div><!-- /login-view -->


            <!-- ── REGISTER VIEW ── -->
            <livewire:enterprise.enterprise-registration/>
          <!-- /register-view -->

        </div><!-- /forms-wrapper -->
    </div><!-- /form-panel -->


    <script>
        const loginView    = document.getElementById('login-view');
        const registerView = document.getElementById('register-view');

        function showRegister() {
            loginView.classList.remove('visible');
            loginView.classList.add('hidden');
            registerView.classList.remove('hidden');
            registerView.classList.add('visible');
        }

        function showLogin() {
            registerView.classList.remove('visible');
            registerView.classList.add('hidden');
            loginView.classList.remove('hidden');
            loginView.classList.add('visible');
        }

        // If there are validation errors on register, show the register form automatically
        @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('password_confirmation') || (session()->has('register_error')))
            showRegister();
        @endif
    </script>

</body>
</html>