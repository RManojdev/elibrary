<style>
  /* E-Library – new UI (Bootstrap 5 + plain CSS only) */
  :root {
    --brand: #4f46e5;
    --brand-600: #4338ca;
    --brand-50: #eef2ff;
    --ink: #0f172a;
    --muted: #64748b;
    --bg: #f5f6fb;
    --line: #e6e8f0;
    --radius: 14px;
    --side: #1e1b4b;
    --shadow: 0 1px 2px rgba(15, 23, 42, .06), 0 10px 28px rgba(15, 23, 42, .06);
    --bs-body-font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif;
    --bs-body-color: var(--ink);
    --bs-body-bg: var(--bg);
    --bs-primary: #4f46e5;
    --bs-primary-rgb: 79, 70, 229;
    --bs-link-color: #4f46e5;
    --bs-link-hover-color: #4338ca;
  }

  body {
    background: var(--bg);
    color: var(--ink)
  }

  h1,
  h2,
  h3,
  h4,
  h5 {
    letter-spacing: -.01em
  }

  .text-muted,
  .text-secondary {
    color: var(--muted) !important
  }

  /* buttons & forms */
  .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: .55rem 1rem
  }

  .btn-primary {
    --bs-btn-bg: var(--brand);
    --bs-btn-border-color: var(--brand);
    --bs-btn-hover-bg: var(--brand-600);
    --bs-btn-hover-border-color: var(--brand-600);
    --bs-btn-active-bg: #3730a3;
    --bs-btn-active-border-color: #3730a3;
    --bs-btn-focus-shadow-rgb: 79, 70, 229
  }

  .btn-outline-primary {
    --bs-btn-color: var(--brand);
    --bs-btn-border-color: #c7cdf8;
    --bs-btn-hover-bg: var(--brand);
    --bs-btn-hover-border-color: var(--brand);
    --bs-btn-active-bg: var(--brand-600)
  }

  .btn-soft {
    background: var(--brand-50);
    color: var(--brand);
    border: 0
  }

  .btn-soft:hover {
    background: #e0e7ff;
    color: var(--brand-600)
  }

  .form-label {
    font-weight: 600;
    font-size: .875rem;
    margin-bottom: .35rem
  }

  .form-control,
  .form-select {
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: .65rem .9rem;
    background-color: #fff
  }

  .form-control:focus,
  .form-select:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 .2rem rgba(79, 70, 229, .15)
  }

  .form-text {
    color: var(--muted)
  }

  /* surfaces */
  .surface {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 1.5rem
  }

  .page-head {
    margin-bottom: 1.5rem
  }

  .page-head h1 {
    font-size: 1.65rem;
    font-weight: 700;
    margin: 0
  }

  .page-head p {
    color: var(--muted);
    margin: .25rem 0 0
  }

  .empty {
    text-align: center;
    padding: 3.5rem 1rem;
    background: #fff;
    border: 1px dashed #cfd4e6;
    border-radius: var(--radius);
    color: var(--muted)
  }

  .empty h3 {
    color: var(--ink);
    font-size: 1.15rem;
    font-weight: 700
  }

  /* brand + top bars */
  .brand-mark {
    width: 180px;
    height: 80px;
    /* border-radius: 10px;
  background: linear-gradient(135deg, #6366f1, #4338ca);
  color: #fff;
  display: inline-grid;
  place-items: center;
  font-weight: 800; */
  }

  .brand-mark-user {
    width: 80px;
    height: 45px;
    /* border-radius: 10px;
  background: linear-gradient(135deg, #6366f1, #4338ca);
  color: #fff;
  display: inline-grid;
  place-items: center;
  font-weight: 800; */
  }

  .brand-name {
    font-weight: 800;
    color: var(--ink);
    letter-spacing: .02em
  }

  .topbar {
    background: #fff;
    border-bottom: 1px solid var(--line);
    position: sticky;
    top: 0;
    z-index: 1030
  }

  .topbar .nav-link {
    color: var(--muted);
    font-weight: 600;
    border-radius: 8px;
    padding: .5rem .85rem
  }

  .topbar .nav-link:hover {
    color: var(--ink);
    background: #f3f4f9
  }

  .topbar .nav-link.active {
    color: var(--brand);
    background: var(--brand-50)
  }

  .avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--brand-50);
    color: var(--brand);
    display: inline-grid;
    place-items: center;
    font-weight: 700;
    text-transform: uppercase
  }

  .user-chip {
    display: flex;
    align-items: center;
    gap: .6rem;
    border: 0;
    background: transparent;
    font-weight: 600;
    color: var(--ink)
  }

  .dropdown-menu {
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
    padding: .4rem
  }

  .dropdown-item {
    border-radius: 8px;
    font-weight: 500
  }

  .container-page {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1rem 1rem 2rem
  }

  /* admin shell */
  .sidebar {
    --bs-offcanvas-width: 268px;
    background: var(--side);
    color: #c7c9ee;
    border: 0
  }

  .sidebar .brand-name {
    color: #fff
  }

  .sidebar-head {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: 1.25rem 1.25rem 1rem
  }

  .side-label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #8386c4;
    padding: 1.1rem 1.5rem .4rem
  }

  .side-link {
    display: block;
    margin: 2px .9rem;
    padding: .6rem .9rem;
    border-radius: 10px;
    color: #c7c9ee;
    text-decoration: none;
    font-weight: 500
  }

  .side-link:hover {
    background: rgba(255, 255, 255, .07);
    color: #fff
  }

  .side-link.active {
    background: var(--brand);
    color: #fff;
    font-weight: 600
  }

  .admin-topbar {
    background: #fff;
    border-bottom: 1px solid var(--line);
    position: sticky;
    top: 0;
    z-index: 1020;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .7rem 1.25rem
  }

  .admin-topbar h2 {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0;
    flex: 1
  }

  @media(min-width:992px) {
    .sidebar {
      visibility: visible !important;
      transform: none !important;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      width: 268px
    }

    .admin-main {
      margin-left: 268px
    }
  }

  /* stats */
  .stat {
    position: relative;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 1.4rem 1.4rem 1.3rem 1.6rem;
    box-shadow: var(--shadow);
    overflow: hidden;
    height: 100%
  }

  .stat::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    background: var(--accent, var(--brand))
  }

  .stat .label {
    color: var(--muted);
    font-weight: 600;
    font-size: .85rem;
    text-transform: uppercase;
    letter-spacing: .06em
  }

  .stat .num {
    font-size: 2.6rem;
    font-weight: 800;
    line-height: 1.1;
    margin: .3rem 0 .6rem
  }

  .stat a {
    font-weight: 600;
    text-decoration: none;
    font-size: .9rem
  }

  /* book cards */
  .book-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .15s, box-shadow .15s
  }

  .book-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow)
  }

  .book-cover {
    position: relative;
    aspect-ratio: 3/4;
    background: var(--brand-50);
    overflow: hidden
  }

  .book-cover img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block
  }

  .book-cat {
    position: absolute;
    left: .7rem;
    bottom: .7rem;
    background: rgba(255, 255, 255, .92);
    color: var(--brand);
    font-weight: 700;
    border-radius: 8px
  }

  .book-flag {
    position: absolute;
    right: .7rem;
    top: .7rem
  }

  .book-body {
    padding: 1rem 1.1rem 1.15rem;
    display: flex;
    flex-direction: column;
    flex: 1
  }

  .book-title {
    font-size: 1.02rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden
  }

  .book-author {
    color: var(--muted);
    font-size: .9rem;
    margin: .2rem 0 .8rem
  }

  .book-meta {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
    margin: 0 0 1rem;
    padding: .7rem 0;
    border-block: 1px solid var(--line)
  }

  .book-meta dt {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    font-weight: 600
  }

  .book-meta dd {
    margin: 0;
    font-weight: 600;
    font-size: .9rem
  }

  .book-actions {
    display: flex;
    gap: .5rem;
    margin-top: auto
  }

  .book-actions form:first-child {
    flex: 1
  }

  /* auth */
  .auth-banner {
    background: #fff;
    border-bottom: 1px solid var(--line);
    padding: .6rem
  }

  .auth-banner img {
    display: block;
    max-width: 100%;
    height: auto;
    margin: 0 auto
  }

  .auth-wrap {
    display: grid;
    grid-template-columns: 1fr;
    min-height: calc(100vh - 90px)
  }

  .auth-art {
    display: none;
    position: relative;
    overflow: hidden;
    color: #fff;
    padding: 4.5rem;
    background: linear-gradient(135deg, #312e81, #4f46e5 60%, #7c3aed)
  }

  .auth-art::before,
  .auth-art::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, .07)
  }

  .auth-art::before {
    width: 420px;
    height: 420px;
    right: -140px;
    top: -120px
  }

  .auth-art::after {
    width: 300px;
    height: 300px;
    left: -100px;
    bottom: -110px
  }

  .auth-art h1 {
    font-size: 2.6rem;
    font-weight: 800;
    line-height: 1.15;
    position: relative
  }

  .auth-art p {
    color: #dfe1ff;
    font-size: 1.1rem;
    max-width: 30rem;
    position: relative
  }

  .auth-form {
    display: grid;
    place-items: center;
    padding: 2rem 1rem
  }

  .auth-card {
    width: 100%;
    max-width: 410px
  }

  .auth-card h2 {
    font-weight: 800;
    font-size: 1.7rem
  }

  @media(min-width:992px) {
    .auth-wrap {
      grid-template-columns: 1.05fr 1fr
    }

    .auth-art {
      display: flex;
      flex-direction: column;
      justify-content: center
    }
  }

  /* tables, badges, pagination (admin lists) */
  .table-modern {
    --bs-table-bg: transparent;
    margin: 0
  }

  .table-modern thead th {
    background: #f8f9fc;
    color: var(--muted);
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 700;
    border-bottom: 1px solid var(--line);
    padding: .85rem 1rem;
    white-space: nowrap
  }

  .table-modern td {
    padding: .8rem 1rem;
    vertical-align: middle;
    border-color: var(--line)
  }

  .table-modern tbody tr:hover>* {
    background: #fafbff
  }

  .badge-soft {
    font-weight: 600;
    padding: .4em .75em;
    border-radius: 999px
  }

  .badge-active {
    background: #dcfce7;
    color: #166534
  }

  .badge-inactive {
    background: #fee2e2;
    color: #991b1b
  }

  .row-actions {
    display: flex;
    gap: .4rem;
    justify-content: flex-end;
    flex-wrap: nowrap
  }

  .row-actions form {
    margin: 0
  }

  .btn-xs {
    padding: .3rem .65rem;
    font-size: .8rem;
    border-radius: 8px
  }

  .table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--line);
    flex-wrap: wrap
  }

  .pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: 1px solid var(--line);
    color: var(--ink);
    font-weight: 600
  }

  .pagination .page-item.active .page-link {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff
  }

  .pagination .page-item.disabled .page-link {
    color: #9ca3af
  }

  /* ===== admin dashboard (bento style) ===== */
  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(10px)
    }

    to {
      opacity: 1;
      transform: none
    }
  }

  @keyframes grow {
    from {
      width: 0
    }
  }

  .dash>* {
    animation: fadeUp .5s ease both
  }

  .dash>*:nth-child(2) {
    animation-delay: .05s
  }

  .dash>*:nth-child(3) {
    animation-delay: .1s
  }

  .dash>*:nth-child(4) {
    animation-delay: .15s
  }

  @media(prefers-reduced-motion:reduce) {

    .dash>*,
    .bar-fill {
      animation: none !important
    }
  }

  .hero {
    position: relative;
    overflow: hidden;
    color: #fff;
    border-radius: 20px;
    padding: 1.9rem 2rem;
    background: linear-gradient(135deg, #312e81 0%, #4f46e5 58%, #7c3aed 100%);
    box-shadow: 0 18px 40px -18px rgba(79, 70, 229, .7)
  }

  .hero::before,
  .hero::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, .08)
  }

  .hero::before {
    width: 320px;
    height: 320px;
    right: -90px;
    top: -130px
  }

  .hero::after {
    width: 200px;
    height: 200px;
    right: 140px;
    bottom: -120px
  }

  .hero>* {
    position: relative;
    z-index: 1
  }

  .hero .eyebrow {
    font-size: .8rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #c7d2fe
  }

  .hero h1 {
    font-size: 1.9rem;
    font-weight: 800;
    margin: .25rem 0 .35rem
  }

  .hero p {
    color: #e0e7ff;
    margin: 0 0 1.2rem;
    max-width: 34rem
  }

  .hero .btn {
    font-weight: 600
  }

  .btn-glass {
    background: rgba(255, 255, 255, .14);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, .28)
  }

  .btn-glass:hover {
    background: rgba(255, 255, 255, .24);
    color: #fff
  }

  .kpi {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 1.25rem 1.3rem;
    box-shadow: var(--shadow);
    height: 100%;
    transition: transform .15s
  }

  .kpi:hover {
    transform: translateY(-3px)
  }

  .kpi-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .9rem
  }

  .kpi-chip {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-weight: 800;
    color: var(--c);
    background: color-mix(in srgb, var(--c) 14%, white)
  }

  .kpi-label {
    color: var(--muted);
    font-weight: 600;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .07em
  }

  .kpi-num {
    font-size: 2.3rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.02em
  }

  .kpi-sub {
    color: var(--muted);
    font-size: .85rem;
    margin-top: .5rem
  }

  .kpi-sub b {
    color: var(--ink)
  }

  .bar-track {
    height: 9px;
    background: #eef0f7;
    border-radius: 999px;
    overflow: hidden
  }

  .bar-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    animation: grow .9s ease-out
  }

  .bar-row {
    margin-bottom: 1rem
  }

  .bar-row:last-child {
    margin-bottom: 0
  }

  .bar-meta {
    display: flex;
    justify-content: space-between;
    font-size: .9rem;
    margin-bottom: .35rem;
    font-weight: 600
  }

  .bar-meta span:last-child {
    color: var(--muted);
    font-weight: 600
  }

  .card-title-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 1.1rem
  }

  .card-title-row h2 {
    font-size: 1.02rem;
    font-weight: 700;
    margin: 0
  }

  .card-title-row a {
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none
  }

  .donut-wrap {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap
  }

  .donut {
    position: relative;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    flex: none
  }

  .donut::after {
    content: "";
    position: absolute;
    inset: 24px;
    background: #fff;
    border-radius: 50%
  }

  .donut-center {
    position: relative;
    z-index: 1;
    text-align: center;
    line-height: 1.1
  }

  .donut-center strong {
    display: block;
    font-size: 1.6rem;
    font-weight: 800
  }

  .donut-center span {
    font-size: .72rem;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em
  }

  .legend {
    list-style: none;
    margin: 0;
    padding: 0;
    flex: 1;
    min-width: 160px
  }

  .legend li {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .3rem 0;
    font-size: .9rem;
    font-weight: 500
  }

  .legend .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex: none
  }

  .legend .cnt {
    margin-left: auto;
    font-weight: 700
  }

  .legend .pc {
    color: var(--muted);
    font-size: .8rem;
    width: 2.8rem;
    text-align: right
  }

  .seg {
    display: flex;
    height: 12px;
    border-radius: 999px;
    overflow: hidden;
    background: #eef0f7
  }

  .seg span {
    display: block;
    height: 100%
  }

  .top-item {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .7rem 0;
    border-bottom: 1px solid var(--line)
  }

  .top-item:last-child {
    border-bottom: 0;
    padding-bottom: 0
  }

  .rank {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--brand-50);
    color: var(--brand);
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: .82rem;
    flex: none
  }

  .top-item:first-child .rank {
    background: #fef3c7;
    color: #b45309
  }

  .thumb {
    width: 40px;
    height: 54px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--brand-50);
    flex: none;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .15)
  }

  .top-body {
    flex: 1;
    min-width: 0
  }

  .top-title {
    font-weight: 700;
    font-size: .92rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
  }

  .top-author {
    color: var(--muted);
    font-size: .8rem;
    margin-bottom: .35rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
  }

  .top-count {
    font-weight: 800;
    font-size: 1.05rem;
    text-align: right;
    flex: none;
    min-width: 2.4rem
  }

  .top-count small {
    display: block;
    color: var(--muted);
    font-size: .68rem;
    font-weight: 600;
    text-transform: uppercase
  }

  .attn {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .8rem .9rem;
    border-radius: 12px;
    text-decoration: none;
    color: var(--ink);
    border: 1px solid var(--line);
    margin-bottom: .65rem;
    transition: background .15s, border-color .15s
  }

  .attn:last-child {
    margin-bottom: 0
  }

  .attn:hover {
    background: #fafbff;
    border-color: #c7cdf8;
    color: var(--ink)
  }

  .attn .n {
    min-width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-weight: 800;
    background: color-mix(in srgb, var(--c) 14%, white);
    color: var(--c)
  }

  .attn .t {
    font-weight: 600;
    font-size: .92rem;
    line-height: 1.25
  }

  .attn .s {
    color: var(--muted);
    font-size: .8rem
  }

  .attn .go {
    margin-left: auto;
    color: var(--muted);
    font-weight: 700
  }

  .allclear {
    text-align: center;
    padding: 1.5rem .5rem;
    color: var(--muted)
  }

  .allclear strong {
    display: block;
    color: #166534;
    font-size: 1.05rem;
    margin-bottom: .15rem
  }

  .acct {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .65rem 0;
    border-bottom: 1px solid var(--line)
  }

  .acct:last-of-type {
    border-bottom: 0
  }

  .acct form {
    margin: 0
  }

  .acct-body {
    flex: 1;
    min-width: 0
  }

  .acct-name {
    font-weight: 600;
    font-size: .92rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
  }

  .acct-meta {
    color: var(--muted);
    font-size: .78rem
  }

  .book-type {
    position: absolute;
    left: .7rem;
    top: .7rem;
    font-weight: 700;
    border-radius: 8px;
    color: #fff
  }

  .type-pdf {
    background: #4f46e5
  }

  .type-audio {
    background: #0d9488
  }

  .type-video {
    background: #e11d48
  }

  .type-choice .btn {
    font-weight: 600
  }

  .info-i {
    position: relative;
    display: inline-grid;
    place-items: center;
    width: 18px;
    height: 18px;
    margin-left: .45rem;
    border-radius: 50%;
    border: 1.5px solid var(--muted);
    color: var(--muted);
    font: italic 700 11px/1 Georgia, serif;
    cursor: help;
    vertical-align: middle;
    user-select: none
  }

  .info-i:hover,
  .info-i:focus {
    border-color: var(--brand);
    color: var(--brand);
    outline: 0
  }

  .info-i .tip {
    position: absolute;
    left: -4px;
    bottom: calc(100% + 9px);
    transform: none;
    width: max-content;
    max-width: 260px;
    padding: .5rem .75rem;
    border-radius: 8px;
    background: #111827;
    color: #fff;
    font: 500 12px/1.45 var(--bs-body-font-family);
    text-align: left;
    white-space: normal;
    box-shadow: var(--shadow);
    opacity: 0;
    visibility: hidden;
    transition: opacity .15s;
    pointer-events: none;
    z-index: 30
  }

  .info-i .tip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 13px;
    margin-left: -5px;
    border: 5px solid transparent;
    border-top-color: #111827
  }

  .info-i:hover .tip,
  .info-i:focus .tip {
    opacity: 1;
    visibility: visible
  }

  .req::after {
    content: " *";
    color: #dc2626;
    font-weight: 700
  }

  .filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .5rem;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: .5rem;
    margin-bottom: .5rem
  }

  .filter-bar .form-control,
  .filter-bar .form-select {
    padding: .4rem .75rem;
    font-size: .875rem;
    border-radius: 8px;
    min-height: 0;
    width: auto;
    background-color: #fff
  }

  .filter-bar .form-select {
    flex: 0 0 auto;
    min-width: 130px;
    padding-right: 2rem
  }

  .filter-bar .grow {
    flex: 1 1 170px;
    min-width: 140px
  }

  .filter-bar .btn {
    padding: .4rem .9rem;
    font-size: .875rem;
    border-radius: 8px
  }

  @media(max-width:575.98px) {

    .filter-bar .form-select,
    .filter-bar .grow {
      flex: 1 1 100%
    }
  }

  .site-footer {
    border-top: 1px solid var(--line);
    background: #fff;
    color: var(--muted);
    font-size: .85rem;
    padding: .9rem 1rem;
    text-align: center
  }

  .site-footer a {
    color: var(--brand);
    font-weight: 600;
    text-decoration: none
  }

  .site-footer a:hover {
    text-decoration: underline
  }

  body.user-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column
  }

  body.user-layout>.container-page {
    flex: 1 0 auto;
    width: 100%
  }

  .admin-main {
    min-height: 100vh;
    display: flex;
    flex-direction: column
  }

  .admin-main>.container-page {
    flex: 1 0 auto;
    width: 100%
  }

  .site-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    flex-wrap: wrap
  }

  .footer-logo {
    height: 22px;
    width: auto;
    display: block
  }
</style>