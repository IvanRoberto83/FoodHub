<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'user_id'     => $this->customer->id,
            'total_price' => 50000,
            'status'      => 'pending',
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

    public function test_admin_can_create_category_with_uploaded_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Minuman',
                'image' => UploadedFile::fake()->image('category.jpg'),
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Minuman')->firstOrFail();
        $this->assertStringStartsWith('/storage/categories/', $category->image);
        $imagePath = parse_url($category->image, PHP_URL_PATH);
        Storage::disk('public')->assertExists(substr($imagePath, strlen('/storage/')));
    }

    public function test_admin_views_normalize_existing_local_image_urls(): void
    {
        $categoryImage = 'http://localhost/storage/categories/legacy-category.jpg';
        $menuImage = 'http://localhost/storage/menus/legacy-menu.jpg';
        $this->category->update(['image' => $categoryImage]);
        $this->menu->update(['image' => $menuImage]);

        $this->actingAs($this->admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('src="/storage/categories/legacy-category.jpg"', false);

        $this->actingAs($this->admin)
            ->get(route('admin.menus.edit', $this->menu))
            ->assertOk()
            ->assertSee('src="/storage/menus/legacy-menu.jpg"', false);
    }

    public function test_admin_can_upload_and_remove_category_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/old-image.jpg', 'old image');
        $this->category->update(['image' => '/storage/categories/old-image.jpg']);
        $image = UploadedFile::fake()->image('category.jpg');

        $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $this->category), [
                'name'  => $this->category->name,
                'image' => $image,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->category->refresh();
        $categoryImagePath = parse_url($this->category->image, PHP_URL_PATH);
        $storedPath = substr($categoryImagePath, strlen('/storage/'));
        Storage::disk('public')->assertExists($storedPath);
        Storage::disk('public')->assertMissing('categories/old-image.jpg');

        $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $this->category), [
                'name'        => $this->category->name,
                'remove_image' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->category->refresh();
        $this->assertSame(
            'https://picsum.photos/seed/' . $this->category->slug . '/300/300',
            $this->category->image
        );
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_admin_category_deletion_removes_its_stored_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/category.jpg', 'category image');
        $category = Category::create([
            'name' => 'Kategori untuk dihapus',
            'slug' => 'kategori-untuk-dihapus',
            'image' => '/storage/categories/category.jpg',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        Storage::disk('public')->assertMissing('categories/category.jpg');
    }

    public function test_admin_can_replace_and_remove_menu_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('menus/old-image.jpg', 'old image');
        $this->menu->update(['image' => '/storage/menus/old-image.jpg']);

        $this->actingAs($this->admin)
            ->put(route('admin.menus.update', $this->menu), [
                'restaurant_id' => $this->restaurant->id,
                'category_id'   => $this->category->id,
                'name'          => $this->menu->name,
                'price'         => $this->menu->price,
                'stock'         => $this->menu->stock,
                'image'         => UploadedFile::fake()->image('menu.jpg'),
            ])
            ->assertRedirect(route('admin.menus.index'));

        $this->menu->refresh();
        $menuImagePath = parse_url($this->menu->image, PHP_URL_PATH);
        $storedPath = substr($menuImagePath, strlen('/storage/'));
        Storage::disk('public')->assertExists($storedPath);
        Storage::disk('public')->assertMissing('menus/old-image.jpg');

        $this->actingAs($this->admin)
            ->put(route('admin.menus.update', $this->menu), [
                'restaurant_id' => $this->restaurant->id,
                'category_id'   => $this->category->id,
                'name'          => $this->menu->name,
                'price'         => $this->menu->price,
                'stock'         => $this->menu->stock,
                'remove_image' => '1',
            ])
            ->assertRedirect(route('admin.menus.index'));

        $this->menu->refresh();
        $this->assertSame(
            'https://picsum.photos/seed/' . Str::slug($this->menu->name) . '/600/400',
            $this->menu->image
        );
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_admin_menu_deletion_cleans_local_images_and_preserves_external_urls(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('menus/local-image.jpg', 'local image');
        $localMenu = Menu::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id'   => $this->category->id,
            'name'          => 'Menu dengan foto lokal',
            'price'         => 10000,
            'stock'         => 5,
            'image'         => '/storage/menus/local-image.jpg',
        ]);
        Storage::disk('public')->put('menus/external.jpg', 'unrelated local file');
        $this->menu->update(['image' => 'https://picsum.photos/seed/external.jpg']);

        $this->actingAs($this->admin)
            ->delete(route('admin.menus.destroy', $localMenu))
            ->assertRedirect(route('admin.menus.index'));

        Storage::disk('public')->assertMissing('menus/local-image.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.menus.destroy', $this->menu))
            ->assertRedirect(route('admin.menus.index'));

        Storage::disk('public')->assertExists('menus/external.jpg');
    }
}
