<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#081a12;
    --bg-deep:#04100a;
    --panel:#0c2417;
    --panel-2:#0f2c1c;
    --panel-alt:#0a2015;
    --border:rgba(212,175,55,.22);
    --border-soft:rgba(212,175,55,.13);
    --border-strong:rgba(212,175,55,.42);
    --gold:#d4af37;
    --gold-bright:#f3cd5c;
    --gold-dim:rgba(212,175,55,.14);
    --green:#2f9e63;
    --green-bright:#3fc27d;
    --green-dim:rgba(63,194,125,.14);
    --amber:#e0a83a;
    --amber-dim:rgba(224,168,58,.15);
    --red:#c0564f;
    --red-dim:rgba(181,52,47,.16);
    --text:#f4f1e6;
    --text-muted:#9fb3a5;
    --text-dim:#6f8577;
    --display:'Rajdhani', sans-serif;
    --mono:'JetBrains Mono', monospace;
    --body:'Inter', sans-serif;
    --sidebar-w:256px;
  }

  *{margin:0;padding:0;box-sizing:border-box;}
  html,body{height:100%;}
  body{
    background:var(--bg);color:var(--text);font-family:var(--body);
    -webkit-font-smoothing:antialiased;
    background-image:
      radial-gradient(ellipse 70% 45% at 15% -10%, rgba(63,194,125,.09), transparent 60%),
      radial-gradient(ellipse 50% 35% at 100% 10%, rgba(212,175,55,.06), transparent 60%);
    background-attachment:fixed;
  }
  ::selection{background:var(--gold);color:var(--bg-deep);}

  /* subtle hex/topographic texture, same as landing page */
  body::before{
    content:"";position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.4;
    background-image:
      linear-gradient(rgba(212,175,55,.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(212,175,55,.03) 1px, transparent 1px);
    background-size:42px 42px;
    mask-image:radial-gradient(ellipse 70% 60% at 20% 0%, #000 0%, transparent 75%);
  }

  .eyebrow{
    font-family:var(--mono);font-size:11px;letter-spacing:.18em;color:var(--gold-bright);
    text-transform:uppercase;display:flex;align-items:center;gap:9px;
  }
  .eyebrow::before{content:"";width:18px;height:1px;background:var(--gold);display:inline-block;}

  .hud-panel{
    position:relative;background:linear-gradient(180deg, rgba(255,255,255,.02), transparent), var(--panel);
    border:1px solid var(--border-soft);border-radius:12px;
    box-shadow:0 1px 0 rgba(255,255,255,.02) inset, 0 10px 30px rgba(0,0,0,.25);
  }
  .hud-panel::before{
    content:"";position:absolute;top:0;left:14px;right:14px;height:1px;
    background:linear-gradient(90deg, transparent, var(--border-strong), transparent);
  }

  /* ===== layout shell ===== */
  .shell{display:flex;min-height:100vh;position:relative;z-index:1;}

  /* ===== sidebar ===== */
  .sidebar{
    width:var(--sidebar-w);flex-shrink:0;background:rgba(6,20,13,.92);backdrop-filter:blur(12px);
    border-right:1px solid var(--border-soft);
    display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow-y:auto;
    transition:transform .25s ease;z-index:40;
  }
  .side-brand{padding:22px 22px 18px;border-bottom:1px solid var(--border-soft);display:flex;align-items:center;gap:11px;}
  .side-brand img{width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border-strong);box-shadow:0 0 0 3px rgba(212,175,55,.08);}
  .side-brand .logo{font-family:var(--display);font-weight:700;font-size:16px;letter-spacing:.03em;text-transform:uppercase;}
  .side-brand .logo span{color:var(--gold-bright);}
  .side-unit{padding:16px 22px;border-bottom:1px solid var(--border-soft);}
  .side-unit .eyebrow{margin-bottom:6px;}
  .side-unit .name{font-family:var(--display);font-weight:700;font-size:16px;line-height:1.3;letter-spacing:.01em;}
  .side-nav{padding:14px 12px;flex:1;}
  .side-nav-label{font-family:var(--mono);font-size:10px;letter-spacing:.14em;color:var(--text-dim);text-transform:uppercase;padding:10px 10px 6px;}
  .side-link{
    display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:9px;
    color:var(--text-muted);font-size:13.5px;font-weight:500;cursor:pointer;border:1px solid transparent;
    text-decoration:none;font-family:var(--body);
  }
  .side-link:hover{background:rgba(255,255,255,.03);color:var(--text);}
  .side-link.active{background:var(--gold-dim);color:var(--gold-bright);border-color:var(--border);font-weight:600;}
  .side-link .dot{width:6px;height:6px;border-radius:50%;background:currentColor;opacity:.6;flex-shrink:0;}
  .side-foot{padding:14px 22px 20px;border-top:1px solid var(--border-soft);}
  .side-user{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
  .side-avatar{width:34px;height:34px;border-radius:50%;background:var(--gold-dim);color:var(--gold-bright);display:flex;align-items:center;justify-content:center;font-family:var(--mono);font-weight:700;font-size:13px;flex-shrink:0;border:1px solid var(--border);}
  .side-user .n{font-size:13px;font-weight:600;line-height:1.3;color:var(--text);}
  .side-user .j{font-size:11.5px;color:var(--text-muted);}
  form.logout button{
    width:100%;font-family:var(--mono);font-size:11.5px;border:1px solid var(--border);background:transparent;
    padding:9px 12px;border-radius:8px;cursor:pointer;color:var(--text-muted);letter-spacing:.04em;
    text-transform:uppercase;transition:border-color .2s ease,color .2s ease;
  }
  form.logout button:hover{border-color:var(--red);color:#e07a72;}

  /* ===== main ===== */
  .main{flex:1;min-width:0;}
  .topbar{
    background:rgba(6,20,13,.82);backdrop-filter:blur(12px);border-bottom:1px solid var(--border-soft);padding:18px 32px;
    display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:30;gap:16px;
  }
  .menu-btn{display:none;background:transparent;border:1px solid var(--border);border-radius:8px;padding:8px 10px;cursor:pointer;color:var(--text);}
  .topbar-title{font-family:var(--display);font-weight:700;font-size:20px;letter-spacing:.01em;}
  .topbar-sub{font-size:12.5px;color:var(--text-muted);margin-top:3px;}

  .badge{
    display:inline-flex;align-items:center;gap:6px;font-family:var(--mono);font-size:10.5px;letter-spacing:.06em;
    background:var(--gold-dim);color:var(--gold-bright);padding:6px 12px;border-radius:999px;
    text-transform:uppercase;border:1px solid var(--border);
  }
  .badge.green{background:var(--green-dim);color:var(--green-bright);border-color:rgba(63,194,125,.28);}
  .badge.amber{background:var(--amber-dim);color:var(--amber);border-color:rgba(224,168,58,.3);}
  .badge.red{background:var(--red-dim);color:#e07a72;border-color:rgba(198,40,40,.3);}

  .content{padding:30px 32px 64px;max-width:1180px;position:relative;z-index:1;}
  .tab-panel{display:none;}
  .tab-panel.active{display:block;animation:fadeIn .25s ease;}
  @keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

  .section-head{margin-bottom:20px;}
  .section-head h2{font-family:var(--display);font-size:22px;font-weight:700;margin-bottom:5px;letter-spacing:.01em;}
  .section-head p{font-size:13px;color:var(--text-muted);}

  /* ===== stat cards ===== */
  .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:26px;}
  .stat-card{
    padding:19px;position:relative;
    background:linear-gradient(180deg, rgba(255,255,255,.02), transparent), var(--panel);
    border:1px solid var(--border-soft);border-radius:12px;
    box-shadow:0 1px 0 rgba(255,255,255,.02) inset, 0 10px 30px rgba(0,0,0,.25);
  }
  .stat-card::before{
    content:"";position:absolute;top:0;left:14px;right:14px;height:1px;
    background:linear-gradient(90deg, transparent, var(--border-strong), transparent);
  }
  .stat-card .lbl{font-family:var(--mono);font-size:10.5px;color:var(--text-dim);text-transform:uppercase;letter-spacing:.06em;margin-bottom:9px;}
  .stat-card .val{font-family:var(--display);font-size:28px;font-weight:700;color:var(--gold-bright);}
  .stat-card .sub{font-size:11.5px;color:var(--text-dim);margin-top:5px;}
  @media(max-width:980px){.stat-grid{grid-template-columns:repeat(2,1fr);}}

  /* ===== panels ===== */
  .panel{
    padding:24px;margin-bottom:20px;position:relative;
    background:linear-gradient(180deg, rgba(255,255,255,.02), transparent), var(--panel);
    border:1px solid var(--border-soft);border-radius:12px;
    box-shadow:0 1px 0 rgba(255,255,255,.02) inset, 0 10px 30px rgba(0,0,0,.25);
  }
  .panel::before{
    content:"";position:absolute;top:0;left:14px;right:14px;height:1px;
    background:linear-gradient(90deg, transparent, var(--border-strong), transparent);
  }
  .panel-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap;}
  .panel-head h3{font-family:var(--display);font-size:17px;font-weight:700;letter-spacing:.01em;}
  .panel-head p{font-size:12px;color:var(--text-muted);margin-top:2px;}

  /* ===== table ===== */
  .tbl-wrap{overflow-x:auto;}
  table.dtbl{width:100%;border-collapse:collapse;font-size:13px;}
  table.dtbl th{
    text-align:left;font-family:var(--mono);font-size:10.5px;letter-spacing:.06em;text-transform:uppercase;
    color:var(--text-dim);padding:11px 12px;border-bottom:1px solid var(--border-soft);white-space:nowrap;
  }
  table.dtbl td{padding:13px 12px;border-bottom:1px solid var(--border-soft);vertical-align:middle;color:var(--text);}
  table.dtbl tr:last-child td{border-bottom:none;}
  table.dtbl tr:hover td{background:rgba(255,255,255,.02);}
  .status-dot{display:inline-flex;align-items:center;gap:7px;font-weight:600;font-size:12.5px;}
  .status-dot::before{content:"";width:8px;height:8px;border-radius:50%;background:currentColor;box-shadow:0 0 8px 1px currentColor;opacity:.9;}
  .status-dot.ok{color:var(--green-bright);}
  .status-dot.warn{color:var(--amber);}
  .status-dot.bad{color:#e07a72;}

  /* ===== buttons ===== */
  .btn{
    font-family:var(--mono);font-weight:600;font-size:11.5px;letter-spacing:.04em;padding:9px 15px;border-radius:8px;
    border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer;
    display:inline-flex;align-items:center;gap:6px;text-decoration:none;text-transform:uppercase;
    transition:transform .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
  }
  .btn:hover{border-color:var(--gold);color:var(--gold-bright);transform:translateY(-1px);}
  .btn-primary{background:linear-gradient(135deg, var(--gold-bright), var(--gold));color:#241a05;border-color:transparent;box-shadow:0 8px 22px -8px rgba(212,175,55,.5);}
  .btn-primary:hover{color:#241a05;box-shadow:0 10px 26px -6px rgba(212,175,55,.6);}
  .btn-ghost-red{color:#e07a72;border-color:rgba(198,40,40,.3);}
  .btn-ghost-red:hover{border-color:#e07a72;color:#e07a72;}
  .btn-sm{padding:7px 12px;font-size:10.5px;}
  .btn-row{display:flex;gap:8px;flex-wrap:wrap;}

  /* ===== forms ===== */
  .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
  .form-field{display:flex;flex-direction:column;gap:6px;}
  .form-field.full{grid-column:1/-1;}
  .form-field label{font-family:var(--mono);font-size:10.5px;letter-spacing:.06em;color:var(--text-dim);text-transform:uppercase;}
  .form-field input,.form-field select,.form-field textarea{
    border:1px solid var(--border);border-radius:8px;padding:11px 13px;font-family:var(--body);font-size:13.5px;
    background:var(--bg-deep);color:var(--text);
  }
  .form-field select option{background:var(--panel-2);color:var(--text);}
  .form-field input:focus,.form-field select:focus,.form-field textarea:focus{outline:none;border-color:var(--gold);}
  .form-field input::placeholder,.form-field textarea::placeholder{color:var(--text-dim);}
  .form-hint{font-size:11px;color:var(--text-dim);}
  @media(max-width:640px){.form-grid{grid-template-columns:1fr;}}

  /* ===== notice ===== */
  .notice{background:var(--gold-dim);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:14px 16px;font-size:12.5px;line-height:1.65;margin-bottom:22px;}
  .notice b{color:var(--gold-bright);}

  /* ===== org tree ===== */
  .org-list{list-style:none;font-size:13px;}
  .org-list li{padding:10px 0;border-bottom:1px solid var(--border-soft);display:flex;justify-content:space-between;align-items:center;gap:10px;}
  .org-list li:last-child{border-bottom:none;}
  .org-list .lvl1{padding-left:0;font-weight:700;font-family:var(--display);font-size:15px;color:var(--text);}
  .org-list .lvl2{padding-left:18px;color:var(--text-muted);}
  .org-list .lvl3{padding-left:36px;color:var(--text-dim);font-size:12.5px;}

  @media(max-width:900px){
    .sidebar{position:fixed;left:0;top:0;transform:translateX(-100%);box-shadow:0 0 40px rgba(0,0,0,.4);}
    .sidebar.open{transform:translateX(0);}
    .menu-btn{display:inline-flex;}
    .stat-grid{grid-template-columns:1fr 1fr;}
    .content{padding:22px 16px 60px;}
    .topbar{padding:14px 16px;}
  }
</style>
<?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\SISTEM SIMULASI\SISTEM_SIBERAD_updated\SISTEM_SIBERAD\SISTEM\resources\views/siberad/dashboards/partials/dash-styles.blade.php ENDPATH**/ ?>