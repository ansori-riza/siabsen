import { test, expect } from '@playwright/test';

test.describe('Login Page E2E Tests', () => {
  test('login page should load successfully', async ({ page }) => {
    await page.goto('/login');
    
    // Check page title
    await expect(page).toHaveTitle(/Login|SiAbsen/);
    
    // Check login form elements exist
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
    
    // Check form has CSRF token (hidden input)
    const csrfInput = page.locator('input[name="_token"]');
    await expect(csrfInput).toBeAttached();
    const csrfValue = await csrfInput.inputValue();
    expect(csrfValue).toBeTruthy();
    expect(csrfValue.length).toBeGreaterThan(20);
  });

  test('should login with valid credentials', async ({ page }) => {
    await page.goto('/login');
    
    // Fill login form
    await page.fill('input[name="email"]', 'admin@siabsen.test');
    await page.fill('input[name="password"]', 'password');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Should redirect to dashboard
    await expect(page).toHaveURL(/.*dashboard|admin/);
    
    // Should see welcome message or dashboard content
    await expect(page.locator('text=/dashboard|welcome|beranda/i').first()).toBeVisible({ timeout: 5000 });
  });

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/login');
    
    // Fill with wrong password
    await page.fill('input[name="email"]', 'admin@siabsen.test');
    await page.fill('input[name="password"]', 'wrongpassword');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Should stay on login page
    await expect(page).toHaveURL(/.*login/);
    
    // Should show error message
    await expect(page.locator('text=/invalid|salah|gagal|error/i').first()).toBeVisible({ timeout: 5000 });
  });

  test('should show error with empty fields', async ({ page }) => {
    await page.goto('/login');
    
    // Submit empty form
    await page.click('button[type="submit"]');
    
    // Should stay on login page
    await expect(page).toHaveURL(/.*login/);
    
    // HTML5 validation should prevent submission or show error
    const emailInput = page.locator('input[name="email"]');
    const passwordInput = page.locator('input[name="password"]');
    
    // Check if required attributes exist
    await expect(emailInput).toHaveAttribute('required');
    await expect(passwordInput).toHaveAttribute('required');
  });

  test('should logout successfully', async ({ page }) => {
    // First login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@siabsen.test');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard|admin/);
    
    // Find and click logout
    const logoutLink = page.locator('a[href*="logout"], button:has-text("logout"), button:has-text("keluar")');
    if (await logoutLink.count() > 0) {
      await logoutLink.click();
      
      // Should redirect to login
      await expect(page).toHaveURL(/.*login/);
    }
  });

  test('remember me checkbox should exist', async ({ page }) => {
    await page.goto('/login');
    
    // Check remember me checkbox
    const rememberCheckbox = page.locator('input[name="remember"]');
    await expect(rememberCheckbox).toBeVisible();
  });
});