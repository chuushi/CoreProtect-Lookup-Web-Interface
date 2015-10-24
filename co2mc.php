<?php
/* Because CoreProtect still uses legacy item names...
 * For CoreProtect 2. */

// Fixed function:
function co2mc($thing) {
    global $co2mc;
    if ($ret = $co2mc[$thing] === NULL) $ret = $co2mc;
    return $ret;
}

function block2co($block) {
}

function item2co($item) {
}

// List of items and block name correction
$co2mc = [
'wood' => 'planks',
'water' => 'flowing_water',
'stationary_water' => 'water',
'lava' => 'flowing_lava',
'stationary_lava' => 'lava',
'note_block' => 'noteblock',
'bed_block' => 'bed',
'piston_sticky_base' => 'sticky_piston',
'piston_base' => 'piston',
'piston_extension' => 'piston_head',
'piston_moving_piece' => 'piston_extension',
'red_rose' => 'red_flower',
'double_step' => 'double_stone_slab', //43
'step' => 'stone_slab',
'wood_stairs' => 'oak_stairs',
'crops' => 'wheat',
'soil' => 'farmland', //60
'burning_furnace' => 'lit_furnace',
'sign_post' => 'standing_sign',
'cobblestone_stairs' => 'stone_stairs',
'stone_plate' => 'stone_pressure_plate',
'iron_door_block' => 'iron_door',
'wood_plate' => 'wooden_pressure_plate',
'glowing_redstone_ore' => 'lit_redstone_ore',
'redstone_torch_off' => 'unlit_redstone_torch',
'redstone_torch_on' => 'redstone_torch',
'snow' => 'snow_layer',
'snow_block' => 'snow',
'sugar_cane_block' => 'reeds',
'jack_o_lantern' => 'lit_pumpkin',
'cake_block' => 'cake',
'diode_block_off' => 'unpowered_repeater',
'diode_block_on' => 'powered_repeater', //94
'trap_door' => 'trapdoor',
'monster_eggs' => 'monster_egg',
'smooth_brick' => 'stonebrick',
'huge_mushroom_1' => 'brown_mushroom_block',
'huge_mushroom_2' => 'red_mushroom_block',
'iron_fence' => 'iron_bars',
'thin_glass' => 'glass_pane',
'smooth_stairs' => 'stone_brick_stairs',
'mycel' => 'mycelium',
'water_lily' => 'waterlily',
'nether_fence' => 'nether_brick_fence',
'nether_warts' => 'nether_wart',
'enchantment_table' => 'enchanting_table',
'ender_portal' => 'end_portal',
'ender_portal_frame' => 'end_portal_frame',
'ender_stone' => 'end_stone', // 121
'redstone_lamp_off' => 'redstone_lamp',
'redstone_lamp_on' => 'lit_redstone_lamp',
'wood_double_step' => 'double_wooden_slab',
'wood_step' => 'wooden_slab',
'spruce_wood_stairs' => 'spruce_stairs',
'birch_wood_stairs' => 'birch_stairs',
'jungle_wood_stairs' => 'jungle_stairs',
'command' => 'command_block',
'cobble_wall' => 'cobblestone_wall',
'carrot' => 'carrots',
'potato' => 'potatoes',
'wood_button' => 'wooden_button',
'gold_plate' => 'light_weighted_pressure_plate',
'iron_plate' => 'heavy_weighted_pressure_plate',
'redstone_comparator_off' => 'unpowered_comparator',
'redstone_comparator_on' => 'powered_comparator',
'stained_clay' => 'stained_hardened_clay',
'leaves_2' => 'leaves2',
'log_2' => 'log2',
'slime_block' => 'slime',
'hard_clay' => 'hardened_clay', //172
// Begin item/entity-only items

'iron_spade' => 'iron_shovel', //256
'wood_sword' => 'wooden_sword',
'wood_spade' => 'wooden_shovel',
'wood_pickaxe' => 'wooden_pickaxe',
'wood_axe' => 'wooden_axe',
'stone_spade' => 'stone_shovel',
'diamond_spade' => 'diamond_shovel',
'gold_sword' => 'golden_sword',
'gold_spade' => 'golden_shovel',
'gold_pickaxe' => 'golden_pickaxe',
'gold_axe' => 'golden_axe',
'sulphur' => 'gunpowder',
'wood_hoe' => 'wooden_hoe',
'gold_hoe' => 'golden_hoe',
'seeds' => 'wheat_seeds',
'gold_helmet' => 'golden_helmet',
'gold_chestplate' => 'golden_chestplate',
'gold_leggings' => 'golden_leggings',
'gold_boots' => 'golden_boots',
'pork' => 'porkchop',
'grilled_pork' => 'cooked_porkchop',
'wood_door' => 'wooden_door',
'snow_ball' => 'snowball',
'clay_brick' => 'brick',
'sugar_cane' => 'reeds',
'storage_minecart' => 'chest_minecart',
'powered_minecart' => 'furnace_minecart',
'watch' => 'clock',
'raw_fish' => 'fish',
'ink_sack' => 'dye',
'diode' => 'repeater',
'map' => 'filled_map',
'raw_beef' => 'beef',
'raw_chicken' => 'chicken',
'nether_stalk' => 'nether_wart',
'brewing_stand_item' => 'brewing_stand',
'cauldron_item' => 'cauldron',
'eye_of_ender' => 'ender_eye',
'monster_egg' => 'spawn_egg',
'exp_bottle' => 'experience_bottle',
'fireball' => 'fire_charge',
'book_and_quill' => 'writable_book',
'flower_pot_item' => 'flower_pot',
'carrot_item' => 'carrot',
'potato_item' => 'potato',
'empty_map' => 'map',
'skull_item' => 'skull',
'carrot_stick' => 'carrot_on_a_stick',
'firework' => 'fireworks',
'redstone_Comparator' => 'comparator',
'nether_brick_item' => 'netherbrick',
'explosive_minecart' => 'tnt_minecart',
'iron_barding' => 'iron_horse_armor',
'gold_barding' => 'golden_horse_armor',
'diamond_barding' => 'diamond_horse_armor',
'leash' => 'lead',
'command_minecart' => 'command_block_minecart',
'spruce_door_item' => 'spruce_door',
'birch_door_item' => 'birch_door',
'jungle_door_item' => 'jungle_door',
'acacia_door_item' => 'acacia_door',
'dark_oak_door_item' => 'dark_oak_door',
'gold_record' => 'record_13',
'green_record' => 'record_cat',
'record_3' => 'record_blocks',
'record_4' => 'record_chirp',
'record_5' => 'record_far',
'record_6' => 'record_mall',
'record_7' => 'record_mellohi',
'record_8' => 'record_stal',
'record_9' => 'record_strad',
'record_10' => 'record_ward',
'record_12' => 'record_wait',

// End item correction list
];

/* I'll probably use this to make it possible for cache to save directly as items mentioned in this array.
 * It's going to take some longer time to decode this every time a lookup is initiated.  Everything is
 * based on ID number anyway.
 */

?>