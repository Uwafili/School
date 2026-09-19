<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('usertype', 'admin')->first() ?? User::query()->first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'FoodStore Seller',
                'email' => 'seller@foodstore.test',
            ]);
        }

        $products = [
            'pizza' => [
                ['Classic Pepperoni Pizza', 'Loaded with mozzarella, pepperoni, and rich tomato sauce.', 6500],
                ['Chicken Suya Pizza', 'Spiced chicken suya, onions, peppers, and melted cheese.', 7200],
                ['Margherita Pizza', 'Fresh tomato, basil, mozzarella, and a light olive oil finish.', 5800],
                ['BBQ Beef Pizza', 'Smoky BBQ beef, caramelized onions, and cheddar cheese.', 7600],
                ['Vegetable Garden Pizza', 'Fresh peppers, sweet corn, mushrooms, tomatoes, and cheese.', 6000],
                ['Seafood Pepper Pizza', 'Seasoned prawns, peppers, herbs, and creamy mozzarella.', 8500],
                ['Four Cheese Pizza', 'A rich blend of mozzarella, cheddar, parmesan, and blue cheese.', 7800],
                ['Spicy Tuna Pizza', 'Tuna, jalapeno, red onions, and a bright tomato base.', 6900],
                ['Chicken Tikka Pizza', 'Tender tikka chicken, yogurt spice, onions, and cheese.', 7400],
                ['Hawaiian Chicken Pizza', 'Grilled chicken, pineapple, smoked beef, and mozzarella.', 7100],
                ['Pepper Mix Pizza', 'Roasted peppers, chili flakes, olives, and stretchy cheese.', 6200],
                ['Loaded Meat Pizza', 'Beef, chicken, sausage, pepperoni, and extra cheese.', 8900],
                ['Garlic Mushroom Pizza', 'Garlic butter mushrooms, herbs, onions, and mozzarella.', 6400],
            ],
            'burger' => [
                ['Smoky BBQ Beef Burger', 'Char-grilled beef patty, BBQ sauce, lettuce, and crispy onions.', 5200],
                ['Crispy Chicken Burger', 'Crunchy chicken fillet, slaw, pickles, and signature sauce.', 4800],
                ['Double Cheese Burger', 'Two beef patties with double cheddar and house burger sauce.', 6800],
                ['Spicy Zinger Burger', 'Hot crispy chicken, spicy mayo, lettuce, and fresh tomato.', 5000],
                ['Bacon Ranch Burger', 'Beef patty, turkey bacon, ranch dressing, and fresh greens.', 6100],
                ['Mushroom Swiss Burger', 'Beef, sauteed mushrooms, Swiss cheese, and garlic mayo.', 5900],
                ['Classic Beef Burger', 'Juicy beef patty, tomato, onion, lettuce, and house sauce.', 4500],
                ['Peri Peri Chicken Burger', 'Grilled peri peri chicken, peppers, and smoky mayo.', 5300],
                ['Fish Fillet Burger', 'Crispy fish fillet, cabbage slaw, and lemon tartar sauce.', 4900],
                ['Loaded Fries Burger', 'Beef burger topped with cheese, fries, and smoky sauce.', 6300],
                ['Avocado Chicken Burger', 'Grilled chicken, avocado, lettuce, and herb dressing.', 5700],
                ['Triple Stack Burger', 'Three seasoned beef patties, cheese, onions, and pickles.', 8200],
                ['Plant Power Burger', 'Crispy vegetable patty, avocado, tomato, and fresh greens.', 4600],
            ],
            'salad' => [
                ['Grilled Chicken Caesar Salad', 'Romaine lettuce, grilled chicken, parmesan, and Caesar dressing.', 4200],
                ['Garden Fresh Salad', 'Crisp greens, cucumber, tomato, carrots, and house dressing.', 3200],
                ['Avocado Crunch Salad', 'Creamy avocado, cabbage, cucumber, seeds, and lime dressing.', 3900],
                ['Tropical Fruit Salad', 'Pineapple, watermelon, mango, banana, and citrus dressing.', 3500],
                ['Greek Feta Salad', 'Tomatoes, cucumber, olives, feta, onions, and oregano.', 4000],
                ['Tuna Protein Salad', 'Tuna, boiled egg, greens, sweet corn, and light vinaigrette.', 4500],
                ['Prawn Mango Salad', 'Juicy prawns, mango, mixed greens, and chili lime dressing.', 6200],
                ['Quinoa Power Bowl', 'Quinoa, chickpeas, greens, roasted vegetables, and tahini.', 4800],
                ['Coleslaw Crunch Bowl', 'Fresh cabbage, carrots, sweet corn, and creamy dressing.', 2800],
                ['Beetroot Orange Salad', 'Roasted beetroot, orange, greens, seeds, and honey dressing.', 3700],
                ['Pasta Garden Salad', 'Cold pasta, peppers, olives, tomatoes, and Italian herbs.', 4100],
                ['Cucumber Mint Salad', 'Cucumber, mint, red onion, and refreshing lemon dressing.', 2600],
            ],
            'drinks' => [
                ['Fresh Mango Smoothie', 'A thick chilled blend of ripe mango and creamy yogurt.', 2500],
                ['Strawberry Banana Smoothie', 'Fresh strawberries, banana, milk, and a touch of honey.', 2700],
                ['Classic Lemonade', 'Freshly squeezed lemon, chilled water, and natural sweetness.', 1800],
                ['Pineapple Ginger Juice', 'Bright pineapple juice with a refreshing ginger kick.', 2200],
                ['Watermelon Cooler', 'Chilled watermelon, lime, and mint for a fresh finish.', 2000],
                ['Iced Mocha Latte', 'Cold coffee, chocolate, milk, and a smooth creamy top.', 3200],
                ['Vanilla Milkshake', 'Thick vanilla shake topped with a light cream swirl.', 3000],
                ['Chocolate Milkshake', 'Rich chocolate, cold milk, and smooth blended ice cream.', 3300],
                ['Hibiscus Zobo', 'Cool hibiscus drink infused with pineapple and spices.', 1500],
                ['Passion Fruit Fizz', 'Passion fruit, soda, lime, and a bright tropical aroma.', 2400],
                ['Peach Iced Tea', 'Chilled black tea with peach, lemon, and gentle sweetness.', 2100],
                ['Coconut Chill', 'Fresh coconut water with tender coconut pieces.', 2300],
            ],
        ];

        foreach ($products as $category => $items) {
            foreach ($items as [$title, $description, $price]) {
                Post::updateOrCreate(
                    ['title' => $title],
                    [
                        'user_id' => $user->id,
                        'description' => $description,
                        'price' => $price,
                        'category' => $category,
                    ],
                );
            }
        }
    }
}
