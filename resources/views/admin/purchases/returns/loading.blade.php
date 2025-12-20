<div class="under-dev-container">
    <div class="under-dev-box">
        <div class="gear">
            <div class="gear-inner"></div>
        </div>
        <h2>Under Development</h2>
        <p>This feature is currently under construction. Please check back later.</p>
    </div>
</div>

<style>
.under-dev-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 280px;
}

.under-dev-box {
    text-align: center;
    padding: 25px;
}

.under-dev-box h2 {
    font-size: 28px;
    font-weight: bold;
    margin-top: 15px;
    color: #444;
}

.under-dev-box p {
    font-size: 16px;
    color: #666;
}

/* Gear Animation */
.gear {
    width: 70px;
    height: 70px;
    border: 6px solid #3498db;
    border-radius: 50%;
    margin: 0 auto;
    position: relative;
    animation: spin 2.2s linear infinite;
}

.gear-inner {
    width: 35px;
    height: 35px;
    background: #3498db;
    border-radius: 50%;
    position: absolute;
    top: 17px;
    left: 17px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
