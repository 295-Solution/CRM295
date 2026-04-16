<style>
    .app-shell {
        display: flex;
        min-height: 100vh;
    }

    .app-sidebar {
        width: 280px;
        background: linear-gradient(180deg, #164f5b, #0f3c46);
        color: #f4fffc;
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        padding: 24px 16px;
        position: sticky;
        top: 0;
        height: 100vh;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 16px;
        transition: width 180ms ease, padding 180ms ease;
    }

    .app-brand-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .app-brand {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .app-sidebar-toggle {
        border: 1px solid rgba(244, 255, 252, 0.3);
        border-radius: 10px;
        background: rgba(244, 255, 252, 0.1);
        color: #f4fffc;
        font-size: 16px;
        font-weight: 700;
        width: 34px;
        height: 34px;
        cursor: pointer;
        transition: transform 160ms ease;
    }

    .app-nav {
        display: grid;
        gap: 8px;
    }

    .app-nav-link {
        display: block;
        text-decoration: none;
        color: #ddf5ef;
        border: 1px solid rgba(244, 255, 252, 0.28);
        border-radius: 12px;
        padding: 10px 12px;
        font-weight: 700;
        background: rgba(244, 255, 252, 0.06);
    }

    .app-nav-link.active {
        background: #f4fffc;
        color: #13434e;
        border-color: #f4fffc;
    }

    .app-nav-group {
        border: 1px solid rgba(244, 255, 252, 0.24);
        border-radius: 12px;
        padding: 8px;
        background: rgba(244, 255, 252, 0.06);
    }

    .app-nav-group.active {
        border-color: rgba(244, 255, 252, 0.5);
        background: rgba(244, 255, 252, 0.14);
    }

    .app-nav-title {
        margin: 0 0 6px;
        padding: 0 4px;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #bfe2da;
        font-weight: 700;
    }

    .app-subnav {
        display: grid;
        gap: 6px;
    }

    .app-subnav a {
        text-decoration: none;
        color: #d8f0ea;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .app-subnav a.active {
        color: #13434e;
        background: #f4fffc;
        border-color: #f4fffc;
    }

    .app-sidebar-footer {
        margin-top: auto;
        display: grid;
        gap: 8px;
    }

    .app-user-chip {
        border: 1px solid rgba(244, 255, 252, 0.28);
        border-radius: 10px;
        padding: 8px 10px;
        background: rgba(244, 255, 252, 0.08);
        font-size: 13px;
        font-weight: 700;
        color: #ddf5ef;
    }

    .app-logout-btn {
        width: 100%;
        border: 1px solid rgba(244, 255, 252, 0.35);
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        color: #f4fffc;
        cursor: pointer;
        background: rgba(244, 255, 252, 0.08);
    }

    .app-main {
        flex: 1;
        min-width: 0;
    }

    .app-shell.sidebar-collapsed .app-sidebar {
        width: 200px;
        padding-inline: 12px;
    }

    .app-shell.sidebar-collapsed .app-sidebar-toggle {
        transform: rotate(180deg);
    }

    @media (max-width: 960px) {
        .app-shell {
            flex-direction: column;
        }

        .app-sidebar {
            position: static;
            height: auto;
            width: 100%;
        }

        .app-sidebar-toggle {
            display: none;
        }
    }
</style>
