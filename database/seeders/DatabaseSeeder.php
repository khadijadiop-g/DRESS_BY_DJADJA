<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SuperAdmin User
        User::updateOrCreate(
            ['email' => 'admin@dressbydjadja.com'],
            [
                'name' => 'DjaDja SuperAdmin',
                'phone' => '+221770000000',
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make('Djadja2026!'),
            ]
        );

        // 2. Demo Client User
        User::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Aïssatou Diop',
                'phone' => '+221771234567',
                'role' => User::ROLE_CLIENT,
                'address' => 'Almadies, Zone 4, Dakar',
                'measurements' => [
                    'poitrine' => 88,
                    'taille' => 70,
                    'hanches' => 96,
                    'longueur' => 168,
                ],
                'password' => Hash::make('Client2026!'),
            ]
        );

        // 3. Settings
        Setting::set('currency_rate_eur_xof', '655.957');
        Setting::set('whatsapp_number', '+221770000000');
        Setting::set('free_shipping_threshold_xof', '50000');

        // 4. Categories (Collections)
        $soiree = Category::updateOrCreate(
            ['slug' => 'robes-de-soiree'],
            [
                'name' => 'Robes de Soirée',
                'description' => 'Silhouettes sculpturales et étoffes lumineuses pour vos soirées et cérémonies d\'exception.',
                'image_url' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $soie = Category::updateOrCreate(
            ['slug' => 'capsule-soie-satin'],
            [
                'name' => 'Capsule Soie & Satin',
                'description' => 'Douceur fluide et tombé impeccable en soies fines et satins haute qualité.',
                'image_url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $intemporelles = Category::updateOrCreate(
            ['slug' => 'les-intemporelles'],
            [
                'name' => 'Les Intemporelles',
                'description' => 'Coupes épurées et minimalisme luxueux pensés pour traverser le temps.',
                'image_url' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // 5. Products Data
        $productsData = [
            [
                'category_id' => $soiree->id,
                'name' => 'Robe DjaDja Signature',
                'slug' => 'robe-djadja-signature',
                'subtitle' => 'Satin Lourd & Col Bénitier',
                'description' => 'La pièce maîtresse de la maison DjaDja. Une robe longue sculpturale au tombé fluide, sublimée par un col bénitier sensuel et une ceinture drapée amovible qui structure la silhouette avec majesté.',
                'fabric_details' => '100% Satin de soie lourd duchesse. Doublure en crêpe doux.',
                'care_guide' => 'Nettoyage à sec uniquement. Repassage doux à la vapeur sur l\'envers.',
                'base_price_xof' => 85000,
                'is_featured' => true,
                'is_new_arrival' => true,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                    ['url' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Champagne Doré', 'color_hex' => '#E5D3B3', 'stock' => 4],
                    ['size' => 'M', 'color_name' => 'Champagne Doré', 'color_hex' => '#E5D3B3', 'stock' => 6],
                    ['size' => 'L', 'color_name' => 'Champagne Doré', 'color_hex' => '#E5D3B3', 'stock' => 3],
                    ['size' => 'M', 'color_name' => 'Noir Ébène', 'color_hex' => '#111111', 'stock' => 5],
                    ['size' => 'L', 'color_name' => 'Noir Ébène', 'color_hex' => '#111111', 'stock' => 0],
                ]
            ],
            [
                'category_id' => $soiree->id,
                'name' => 'Robe Nafi Soirée',
                'slug' => 'robe-nafi-soiree',
                'subtitle' => 'Soie Plissée & Dos Nu Subtil',
                'description' => 'Une allure impériale et aérienne. La robe Nafi captive par ses micro-plis faits main et son dos ouvert orné d\'un nœud fin délicat.',
                'fabric_details' => 'Mousseline de soie plissée soleil. Finitions roulottées à la main.',
                'care_guide' => 'Nettoyage professionnel délicat. Ne pas repasser les plis à sec.',
                'base_price_xof' => 65000,
                'is_featured' => true,
                'is_new_arrival' => true,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Vert Émeraude', 'color_hex' => '#1B4D3E', 'stock' => 3],
                    ['size' => 'M', 'color_name' => 'Vert Émeraude', 'color_hex' => '#1B4D3E', 'stock' => 5],
                    ['size' => 'L', 'color_name' => 'Vert Émeraude', 'color_hex' => '#1B4D3E', 'stock' => 2],
                    ['size' => 'M', 'color_name' => 'Or Sable', 'color_hex' => '#D4AF37', 'stock' => 4],
                ]
            ],
            [
                'category_id' => $soie->id,
                'name' => 'Robe Awa Portefeuille',
                'slug' => 'robe-awa-portefeuille',
                'subtitle' => 'Crêpe de Soie & Manches Kimono',
                'description' => 'L\'élégance sans effort. La robe Awa épouse harmonieusement toutes les morphologies grâce à sa coupe cache-cœur fluide et ses manches kimono amples.',
                'fabric_details' => 'Crêpe georgette de soie haute densité. Toucher mat et soyeux.',
                'care_guide' => 'Lavage à la main à l\'eau froide ou pressing.',
                'base_price_xof' => 55000,
                'is_featured' => true,
                'is_new_arrival' => false,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Ivoire Perlé', 'color_hex' => '#F8F6F0', 'stock' => 4],
                    ['size' => 'M', 'color_name' => 'Ivoire Perlé', 'color_hex' => '#F8F6F0', 'stock' => 6],
                    ['size' => 'L', 'color_name' => 'Ivoire Perlé', 'color_hex' => '#F8F6F0', 'stock' => 3],
                    ['size' => 'XL', 'color_name' => 'Terracotta', 'color_hex' => '#C86D51', 'stock' => 4],
                ]
            ],
            [
                'category_id' => $soie->id,
                'name' => 'Robe Mariama Asymétrique',
                'slug' => 'robe-mariama-asymetrique',
                'subtitle' => 'Fourreau Drapé & Fente Latérale',
                'description' => 'Audacieuse et raffinée, la robe Mariama présente une épaule asymétrique et un jeu de drapé architectural cintré à la taille.',
                'fabric_details' => '95% Soie stretch, 5% Élasthanne pour un maintien parfait.',
                'care_guide' => 'Nettoyage à sec spécialisé.',
                'base_price_xof' => 75000,
                'is_featured' => true,
                'is_new_arrival' => true,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Bordeaux Impérial', 'color_hex' => '#5C0614', 'stock' => 2],
                    ['size' => 'M', 'color_name' => 'Bordeaux Impérial', 'color_hex' => '#5C0614', 'stock' => 5],
                    ['size' => 'L', 'color_name' => 'Noir Profond', 'color_hex' => '#141414', 'stock' => 3],
                ]
            ],
            [
                'category_id' => $intemporelles->id,
                'name' => 'Robe Coumba Minimaliste',
                'slug' => 'robe-coumba-minimaliste',
                'subtitle' => 'Lin Naturel & Décolleté Carré',
                'description' => 'La quintessence du Quiet Luxury. Une silhouette droite et pure, coupée dans un mélange noble de lin et de soie, parfaite pour les journées lumineuses à Dakar ou en bord de mer.',
                'fabric_details' => '60% Lin lavé, 40% Soie sauvage.',
                'care_guide' => 'Lavage délicat à 30°C. Séchage naturel sur cintre.',
                'base_price_xof' => 45000,
                'is_featured' => false,
                'is_new_arrival' => false,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Blanc Albâtre', 'color_hex' => '#F4F2EC', 'stock' => 4],
                    ['size' => 'M', 'color_name' => 'Blanc Albâtre', 'color_hex' => '#F4F2EC', 'stock' => 7],
                    ['size' => 'L', 'color_name' => 'Blanc Albâtre', 'color_hex' => '#F4F2EC', 'stock' => 4],
                    ['size' => 'M', 'color_name' => 'Terre Brûlée', 'color_hex' => '#8D4024', 'stock' => 3],
                ]
            ],
            [
                'category_id' => $intemporelles->id,
                'name' => 'Robe Yacine Chemisier',
                'slug' => 'robe-yacine-chemisier',
                'subtitle' => 'Soie Sauvage & Boutons Nacre',
                'description' => 'Un classique intemporel réinventé. Col officier montant, poches coutures discrètes et boutonnage caché sous patte.',
                'fabric_details' => '100% Soie sauvage d\'origine certifiée.',
                'care_guide' => 'Pressing délicat.',
                'base_price_xof' => 58000,
                'is_featured' => false,
                'is_new_arrival' => true,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1200&q=85', 'is_hover' => false],
                    ['url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=85', 'is_hover' => true],
                ],
                'variants' => [
                    ['size' => 'S', 'color_name' => 'Vert Sauge', 'color_hex' => '#9CAF88', 'stock' => 4],
                    ['size' => 'M', 'color_name' => 'Vert Sauge', 'color_hex' => '#9CAF88', 'stock' => 5],
                    ['size' => 'L', 'color_name' => 'Écru Naturel', 'color_hex' => '#EAE6DF', 'stock' => 3],
                ]
            ],
        ];

        foreach ($productsData as $data) {
            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'subtitle' => $data['subtitle'],
                    'description' => $data['description'],
                    'fabric_details' => $data['fabric_details'],
                    'care_guide' => $data['care_guide'],
                    'base_price_xof' => $data['base_price_xof'],
                    'is_featured' => $data['is_featured'],
                    'is_active' => true,
                    'is_new_arrival' => $data['is_new_arrival'],
                ]
            );

            // Re-seed images
            ProductImage::where('product_id', $product->id)->delete();
            foreach ($data['images'] as $idx => $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $img['url'],
                    'alt_text' => $product->name,
                    'is_hover' => $img['is_hover'],
                    'sort_order' => $idx,
                ]);
            }

            // Re-seed variants
            ProductVariant::where('product_id', $product->id)->delete();
            foreach ($data['variants'] as $v) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $v['size'],
                    'color_name' => $v['color_name'],
                    'color_hex' => $v['color_hex'],
                    'sku' => strtoupper(Str::slug($product->name . '-' . $v['color_name'] . '-' . $v['size'])),
                    'stock_quantity' => $v['stock'],
                ]);
            }
        }
    }
}
