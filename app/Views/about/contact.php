<h1>Aloqa</h1>
<p style="text-align: center; font-size: 1.2rem; color: #6c7086; margin-bottom: 30px;">Savollaringiz bo'lsa, biz bilan bog'laning</p>
<form action="/contact" method="post" style="background: #f9fafb; padding: 30px; border-radius: 12px;">
    <div style="margin-bottom: 20px;">
        <label for="name" style="display: block; margin-bottom: 5px; font-weight: 600;">Ismingiz:</label>
        <input type="text" id="name" name="name" required style="width: 100%; padding: 12px; border: 1px solid #e0e7ff; border-radius: 8px; font-size: 1rem;">
    </div>
    <div style="margin-bottom: 20px;">
        <label for="email" style="display: block; margin-bottom: 5px; font-weight: 600;">Email:</label>
        <input type="email" id="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #e0e7ff; border-radius: 8px; font-size: 1rem;">
    </div>
    <div style="margin-bottom: 20px;">
        <label for="message" style="display: block; margin-bottom: 5px; font-weight: 600;">Xabar:</label>
        <textarea id="message" name="message" required style="width: 100%; padding: 12px; border: 1px solid #e0e7ff; border-radius: 8px; font-size: 1rem; min-height: 120px;"></textarea>
    </div>
    <div style="text-align: center;">
        <button type="submit" class="btn">Yuborish</button>
    </div>
</form>