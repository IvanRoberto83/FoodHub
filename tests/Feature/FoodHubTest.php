<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodHubTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Restaurant $restaurant;
    protected Category $category;
    protected Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Akun Admin
        $this->admin = User::factory()->create([
            'name'     => 'Admin Test',
            'email'    => 'admin@foodhub.com',
            'role'     => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Akun Customer
        $this->customer = User::factory()->create([
            'name'     => 'Customer Test',
            'email'    => 'customer@foodhub.com',
            'role'     => 'customer',
            'password' => bcrypt('password'),
        ]);

        // 3. Restoran & Kategori & Menu
        $owner = User::factory()->create(['role' => 'owner']);
        $this->restaurant = Restaurant::create([
            'user_id' => $owner->id,
            'name'    => 'Resto Uji Coba',
            'slug'    => 'resto-uji-coba',
            'address' => 'Jl. Kaliurang No. 1',
            'phone'   => '08123456789',
        ]);

        $this->category = Category::create([
            'name' => 'Nasi',
            'slug' => 'nasi',
        ]);

        $this->menu = Menu::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id'   => $this->category->id,
            'name'          => 'Nasi Goreng Spesial',
            'price'         => 25000,
            'stock'         => 20,
            'is_available'  => true,
            'image'         => 'https://picsum.photos/300/300',
        ]);
    }

    /**
     * Tes 1: Halaman publik dapat diakses tanpa login
     */
    public function test_public_pages_can_be_accessed(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/menus')->assertStatus(200);
        $this->get("/menus/{$this->menu->id}")->assertStatus(200);
    }

    /**
     * Tes 2: Guest diblokir dari keranjang dan diarahkan ke login
     */
    public function test_guest_cannot_access_cart(): void
    {
        $this->get('/cart')->assertRedirect('/login');
    }

    /**
     * Tes 3: Customer dapat menambah menu ke keranjang
     */
    public function test_customer_can_add_menu_to_cart(): void
    {
        $response = $this->actingAs($this->customer)
            ->post("/cart/add/{$this->menu->id}", [
                'quantity' => 2,
            ]);

        $response->assertRedirect('/cart');
        $this->assertDatabaseHas('cart_items', [
            'menu_id'  => $this->menu->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Tes 4: Customer dapat melakukan checkout pesanan
     */
    public function test_customer_can_checkout_cart(): void
    {
        // Masukkan ke keranjang
        $this->actingAs($this->customer)
            ->post("/cart/add/{$this->menu->id}", ['quantity' => 2]);

        // Lakukan checkout
        $response = $this->actingAs($this->customer)
            ->post('/checkout', [
                'delivery_address' => 'Jl. Gejayan No. 10 Yogyakarta',
                'payment_method'   => 'cod',
            ]);

        $response->assertRedirect('/my-orders');

        // Pastikan pesanan masuk database
        $this->assertDatabaseHas('orders', [
            'user_id'          => $this->customer->id,
            'total_price'      => 50000,
            'status'           => 'pending',
        ]);

        // Pastikan stok berkurang dari 20 menjadi 18
        $this->assertDatabaseHas('menus', [
            'id'    => $this->menu->id,
            'stock' => 18,
        ]);
    }

    /**
     * Tes 5: Customer biasa dilarang masuk ke dashboard admin (403 Forbidden)
     */
    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->customer)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    /**
     * Tes 6: Admin dapat mengakses dashboard admin
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }
}
