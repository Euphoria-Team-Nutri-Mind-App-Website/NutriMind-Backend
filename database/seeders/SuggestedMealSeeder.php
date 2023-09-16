<?php

namespace Database\Seeders;

use App\Models\SuggestedMeal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuggestedMealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuggestedMeal::create([
            'name' => 'Corned beef hash canned',
            'details' => 'Corned beef hash (canned) is a convenient and satisfying meal option made from canned corned beef that has been mixed with diced potatoes and seasonings. It is a popular dish known for its hearty and savory flavors. The canned corned beef is typically cooked and crumbled, then combined with cooked potatoes, onions, and various herbs and spices. The mixture is then pan-fried or baked until golden brown and crispy. The result is a flavorful combination of tender corned beef, soft potatoes, and a delicious blend of seasonings. Corned beef hash can be enjoyed as a standalone dish or served with eggs for a classic breakfast option. It provides a comforting and filling meal that is quick and easy to prepare. The quantaties below is only for 85gm.',
            'calories' => 120,
            'protein' => 12,
            'fats' => 8,
            'carbs' => 6,
            'image' => 'https://images.unsplash.com/photo-1676300185292-e23bb3db50fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fENvcm5lZCUyMGJlZWYlMjBoYXNoJTIwY2FubmVkfGVufDB8fDB8fHww&auto=format&fit=crop&w=500&q=60',
        ]);
        SuggestedMeal::create([
            'name' => 'Fried, breast or leg and thigh chicken',
            'details' => 'Fried chicken, whether made with breast or leg and thigh cuts, is a delicious and popular meal option. The chicken is typically coated in a seasoned flour or batter and deep-fried until golden and crispy. The result is a crispy and flavorful outer layer that encases tender and juicy meat. Whether you prefer the lean and slightly milder taste of the breast or the richer and more succulent flavor of the leg and thigh, fried chicken offers a satisfying and indulgent dining experience. It pairs well with a variety of side dishes such as mashed potatoes, coleslaw, or cornbread, making it a versatile and comforting meal choice. The quantaties below is only for 85gm.',
            'calories' => 245,
            'protein' => 25,
            'fats' => 15,
            'carbs' => 0,
            'image' => 'https://images.unsplash.com/photo-1610057099443-fde8c4d50f91?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzJ8fEZyaWVkJTJDJTIwYnJlYXN0JTIwb3IlMjBsZWclMjBhbmQlMjB0aGlnaCUyMGNoaWNrZW58ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=500&q=60',
        ]);
        SuggestedMeal::create([
            'name' => 'Spaghetti with meat sauce',
            'details' => 'Spaghetti with meat sauce is a classic and comforting meal. It consists of cooked spaghetti noodles topped with a rich and savory meat sauce. The meat sauce is typically made by browning ground meat (such as beef, pork, or a combination) with onions, garlic, and seasonings. Tomato sauce or crushed tomatoes are then added, along with herbs and spices to enhance the flavor. The sauce is simmered to allow the flavors to meld together and develop a delicious depth. The cooked spaghetti noodles are then tossed in the meat sauce, ensuring that each strand is coated with the flavorful sauce. This hearty and satisfying dish can be garnished with grated cheese and fresh herbs, and it pairs well with a side of garlic bread or a green salad. It is a go-to option for pasta lovers and a favorite for family meals or gatherings. The quantaties below is only for 250gm.',
            'calories' => 285,
            'protein' => 13,
            'fats' => 10,
            'carbs' => 35,
            'image' => 'https://images.unsplash.com/photo-1575195701176-56e390e74b66?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8U3BhZ2hldHRpJTIwd2l0aCUyMG1lYXQlMjBzYXVjZXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=500&q=60',
        ]);
        SuggestedMeal::create([
            'name' => 'chicken soup',
            'details' => ' The quantaties below is only for 250gm.',
            'calories' => 75,
            'protein' => 4,
            'fats' => 2,
            'carbs' => 10,
            'image' => 'https://images.unsplash.com/photo-1584949602334-4e99f98286a9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OTZ8fGNoaWNrZW4lMjBzb3VwfGVufDB8fDB8fHww&auto=format&fit=crop&w=500&q=60',
        ]);
        SuggestedMeal::create([
            'name' => 'Turkey breast (cooked, stewed, or broiled)',
            'details' => 'Turkey breast (cooked, stewed, or broiled) is a lean and flavorful meal option made from turkey breast meat. It is known for its tender texture and mild, yet delicious taste. When cooked, the turkey breast can be prepared in various ways to suit different preferences. It can be roasted or broiled to achieve a golden and slightly crispy exterior while maintaining its juicy and succulent interior. Stewed turkey breast involves simmering the meat in a flavorful liquid, allowing it to absorb the flavors and become tender and moist. The choice of cooking method can greatly influence the taste and texture of the turkey breast. It pairs well with a variety of sides such as roasted vegetables, mashed potatoes, or cranberry sauce. Whether enjoyed as a centerpiece of a festive meal or as a healthy protein option, turkey breast offers a satisfying and nutritious dining experience. The quantaties below is only for 100gm.',
            'calories' => 135,
            'protein' => 30,
            'fats' => 0.74,
            'carbs' => 2.9,
            'image' => 'https://www.allrecipes.com/thmb/uHk4tL_TxKLc9rlsZuuVLx0mEXY=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/229658-oven-roasted-turkey-breasts-DDMFS-4x3-091a8e4f7c9943dfa1c11d4957ba505b.jpg',
        ]);
        SuggestedMeal::create([
            'name' => 'Tuna Canned in Oil Drained',
            'details' => 'Introducing the Tuna Salad Delight, a delectable and nutritious meal that combines the delicate flavors of canned tuna with a medley of fresh ingredients. This particular rendition features tuna canned in oil, expertly drained to create a harmonious balance of taste and texture.

            The star of this dish is the tuna, which takes center stage with its tender and succulent flesh. The oil-drained tuna retains a hint of richness from its original medium, enhancing its natural flavors without overpowering them. Each bite offers a satisfying mouthfeel, complemented by a mild oil-infused essence.

            The tuna is then expertly paired with an array of vibrant vegetables and herbs, resulting in a refreshing and visually appealing salad. Crisp lettuce leaves provide a refreshing base, while the addition of vibrant cherry tomatoes adds a burst of sweetness. Thinly sliced cucumbers bring a cool and crisp element to the dish, perfectly balancing the richness of the tuna.
            The quantaties below is only for 100gm.',
            'calories' => 198,
            'protein' => 29,
            'fats' => 8,
            'carbs' => 2.4,
            'image' => 'https://www.tastingtable.com/img/gallery/why-you-need-to-fully-drain-the-tuna-can-for-tuna-salad/l-intro-1654714303.jpg',
        ]);
        SuggestedMeal::create([
            'name' => 'Alyoum Chicken Premium Chicken Kabab',
            'details' => 'Indulge in the sensational flavors of our Alyoum Chicken Premium Chicken Kabab. Tender, handpicked chicken marinated in a blend of aromatic spices, grilled to perfection, and served with fragrant basmati rice and fresh salad garnishes. Experience the epitome of culinary excellence with this succulent and flavorful dish. The quantaties below is only for 100gm.',
            'calories' => 176,
            'protein' => 17.5,
            'fats' => 8.9,
            'carbs' => 2.6,
            'image' => 'https://images.deliveryhero.io/image/talabat/Menuitems/chicken_tikka_637921122908591376.jpg',
        ]);
        SuggestedMeal::create([
            'name' => 'Beef (cooked ,stewed or broiled)',
            'details' => ' The quantaties below is only for 100gm.',
            'calories' => 250,
            'protein' => 13,
            'fats' => 25,
            'carbs' => 0,
            'image' => 'https://www.seriouseats.com/thmb/m52A69B3B1G1azKH-y2wg9QA4K8=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/__opt__aboutcom__coeus__resources__content_migration__serious_eats__seriouseats.com__images__2016__01__20160116-american-beef-stew-recipe-32-9790a4abcb7a4cf198256e9e74a2c921.jpg',
        ]);
    }
}
