<style>
    #global-page-loader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: linear-gradient(135deg, #0f0c29, #24243e);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity .35s ease, visibility .35s ease;
    }
    #global-page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
    #global-page-loader .gpl-ring {
        width: 44px; height: 44px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,.15);
        border-top-color: #818cf8;
        animation: gplSpin .8s linear infinite;
        margin: 0 auto 14px;
    }
    #global-page-loader .gpl-text {
        color: rgba(255,255,255,.55);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-align: center;
        font-family: 'Inter', sans-serif;
    }
    @keyframes gplSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @media (prefers-reduced-motion: reduce) {
        #global-page-loader .gpl-ring { animation: none; border-top-color: rgba(255,255,255,.6); }
    }
</style>
