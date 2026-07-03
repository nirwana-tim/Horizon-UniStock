<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemVariant;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Almamater' => ItemCategory::where('name', 'Almamater')->first(),
            'Jas Lab' => ItemCategory::where('name', 'Jas Lab')->first(),
            'KTM' => ItemCategory::where('name', 'KTM')->first(),
            'Lanyard & Holder' => ItemCategory::where('name', 'Lanyard & Holder')->first(),
            'Merchandise' => ItemCategory::where('name', 'Merchandise')->first(),
            'Midwifery Kit' => ItemCategory::where('name', 'Midwifery Kit')->first(),
            'Name Tag' => ItemCategory::where('name', 'Name Tag')->first(),
            'Nursing Kit' => ItemCategory::where('name', 'Nursing Kit')->first(),
            'Scrub Suit' => ItemCategory::where('name', 'Scrub Suit')->first(),
            'Seragam Komunitas' => ItemCategory::where('name', 'Seragam Komunitas')->first(),
            'Seragam Kuliah' => ItemCategory::where('name', 'Seragam Kuliah')->first(),
            'Seragam Praktek' => ItemCategory::where('name', 'Seragam Praktek')->first(),
        ];

        // Kits (single size)
        $KIT_U_MID_02 = Item::firstOrCreate(
            ['code' => 'KIT-U-MID-02-01'],
            ['name' => 'Kit Midwifery Unisex STIKES', 'category_id' => $categories['Midwifery Kit']->id, 'unit' => 'set', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KIT-U-MID-02-01'], ['item_id' => $KIT_U_MID_02->id, 'size' => '01', 'size_label' => 'All Size']);

        $KIT_U_NUR_02 = Item::firstOrCreate(
            ['code' => 'KIT-U-NUR-02-01'],
            ['name' => 'Kit Nursing Unisex STIKES', 'category_id' => $categories['Nursing Kit']->id, 'unit' => 'set', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KIT-U-NUR-02-01'], ['item_id' => $KIT_U_NUR_02->id, 'size' => '01', 'size_label' => 'All Size']);

        $KIT_U_NUR_05 = Item::firstOrCreate(
            ['code' => 'KIT-U-NUR-05-01'],
            ['name' => 'Kit Nursing Unisex S1 KEP', 'category_id' => $categories['Nursing Kit']->id, 'unit' => 'set', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KIT-U-NUR-05-01'], ['item_id' => $KIT_U_NUR_05->id, 'size' => '01', 'size_label' => 'All Size']);

        $KIT_U_NUR_06 = Item::firstOrCreate(
            ['code' => 'KIT-U-NUR-06-01'],
            ['name' => 'Kit Nursing Unisex D3 KEP', 'category_id' => $categories['Nursing Kit']->id, 'unit' => 'set', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KIT-U-NUR-06-01'], ['item_id' => $KIT_U_NUR_06->id, 'size' => '01', 'size_label' => 'All Size']);

        $KIT_U_NUR_09 = Item::firstOrCreate(
            ['code' => 'KIT-U-NUR-09-01'],
            ['name' => 'Kit Nursing Unisex NERS', 'category_id' => $categories['Nursing Kit']->id, 'unit' => 'set', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KIT-U-NUR-09-01'], ['item_id' => $KIT_U_NUR_09->id, 'size' => '01', 'size_label' => 'All Size']);

        // KTM, Tags, Lanyard (single size)
        $KTM_U_KTM_01 = Item::firstOrCreate(
            ['code' => 'KTM-U-KTM-01-01'],
            ['name' => 'KTM Kartu Mahasiswa Unisex Horizon', 'category_id' => $categories['KTM']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KTM-U-KTM-01-01'], ['item_id' => $KTM_U_KTM_01->id, 'size' => '01', 'size_label' => 'All Size']);

        $KTM_U_TAG_02 = Item::firstOrCreate(
            ['code' => 'KTM-U-TAG-02-01'],
            ['name' => 'KTM Tag Unisex STIKES', 'category_id' => $categories['Name Tag']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KTM-U-TAG-02-01'], ['item_id' => $KTM_U_TAG_02->id, 'size' => '01', 'size_label' => 'All Size']);

        $KTM_U_YDH_01 = Item::firstOrCreate(
            ['code' => 'KTM-U-YDH-01-01'],
            ['name' => 'KTM Lanyard & Holder Unisex Horizon', 'category_id' => $categories['Lanyard & Holder']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'KTM-U-YDH-01-01'], ['item_id' => $KTM_U_YDH_01->id, 'size' => '01', 'size_label' => 'All Size']);

        // Merchandise
        $MRC_U_TBR_01 = Item::firstOrCreate(
            ['code' => 'MRC-U-TBR-01-TM'],
            ['name' => 'Merchandise Tumbler Unisex Horizon', 'category_id' => $categories['Merchandise']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'MRC-U-TBR-01-TM'], ['item_id' => $MRC_U_TBR_01->id, 'size' => 'TM', 'size_label' => 'TM']);

        // Shoes Laki-laki
        $SHO_L_CLC_02 = Item::firstOrCreate(
            ['code' => 'SHO-L-CLC-02-37'],
            ['name' => 'Shoes Clinical Laki - Laki STIKES', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-L-CLC-02-37'], ['item_id' => $SHO_L_CLC_02->id, 'size' => '37', 'size_label' => '37']);

        $SHO_L_CLC_14 = Item::firstOrCreate(
            ['code' => 'SHO-L-CLC-14-39'],
            ['name' => 'Shoes Clinical Laki - Laki S1 Pariwisata', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-L-CLC-14-39'], ['item_id' => $SHO_L_CLC_14->id, 'size' => '39', 'size_label' => '39']);

        $SHO_L_CLG_02 = Item::firstOrCreate(
            ['code' => 'SHO-L-CLG-02-37'],
            ['name' => 'Shoes College Laki - Laki STIKES', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-L-CLG-02-37'], ['item_id' => $SHO_L_CLG_02->id, 'size' => '37', 'size_label' => '37']);

        // Shoes Perempuan
        $SHO_P_CLC_02 = Item::firstOrCreate(
            ['code' => 'SHO-P-CLC-02-34'],
            ['name' => 'Shoes Clinical Perempuan STIKES', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-P-CLC-02-34'], ['item_id' => $SHO_P_CLC_02->id, 'size' => '34', 'size_label' => '34']);

        $SHO_P_CLC_14 = Item::firstOrCreate(
            ['code' => 'SHO-P-CLC-14-36'],
            ['name' => 'Shoes Clinical Perempuan S1 Pariwisata', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-P-CLC-14-36'], ['item_id' => $SHO_P_CLC_14->id, 'size' => '36', 'size_label' => '36']);

        $SHO_P_CLG_02 = Item::firstOrCreate(
            ['code' => 'SHO-P-CLG-02-34'],
            ['name' => 'Shoes College Perempuan STIKES', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-P-CLG-02-34'], ['item_id' => $SHO_P_CLG_02->id, 'size' => '34', 'size_label' => '34']);

        // Shoes Unisex Scrub
        $SHO_U_SCB_02 = Item::firstOrCreate(
            ['code' => 'SHO-U-SCB-02-34'],
            ['name' => 'Shoes Scrub Unisex STIKES', 'category_id' => $categories['Scrub Suit']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'SHO-U-SCB-02-34'], ['item_id' => $SHO_U_SCB_02->id, 'size' => '34', 'size_label' => '34']);

        // Uniforms Laki-laki
        $UNF_L_CLC_02 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLC-02-03'],
            ['name' => 'Uniform Clinical Laki - Laki STIKES', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLC-02-03'], ['item_id' => $UNF_L_CLC_02->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLC_03 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLC-03-03'],
            ['name' => 'Uniform Clinical Laki - Laki STMIK', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLC-03-03'], ['item_id' => $UNF_L_CLC_03->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLC_14 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLC-14-03'],
            ['name' => 'Uniform Clinical Laki - Laki S1 Pariwisata', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLC-14-03'], ['item_id' => $UNF_L_CLC_14->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLG_01 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLG-01-03'],
            ['name' => 'Uniform College Laki - Laki Horizon', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLG-01-03'], ['item_id' => $UNF_L_CLG_01->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLG_02 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLG-02-03'],
            ['name' => 'Uniform College Laki - Laki STIKES', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLG-02-03'], ['item_id' => $UNF_L_CLG_02->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLG_03 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLG-03-03'],
            ['name' => 'Uniform College Laki - Laki STMIK', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLG-03-03'], ['item_id' => $UNF_L_CLG_03->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_CLG_04 = Item::firstOrCreate(
            ['code' => 'UNF-L-CLG-04-03'],
            ['name' => 'Uniform College Laki - Laki STIE', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-CLG-04-03'], ['item_id' => $UNF_L_CLG_04->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_COM_05 = Item::firstOrCreate(
            ['code' => 'UNF-L-COM-05-03'],
            ['name' => 'Uniform Community Laki - Laki S1 KEP', 'category_id' => $categories['Seragam Komunitas']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-COM-05-03'], ['item_id' => $UNF_L_COM_05->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_COM_06 = Item::firstOrCreate(
            ['code' => 'UNF-L-COM-06-03'],
            ['name' => 'Uniform Community Laki - Laki D3 KEP', 'category_id' => $categories['Seragam Komunitas']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-COM-06-03'], ['item_id' => $UNF_L_COM_06->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_L_SCB_02 = Item::firstOrCreate(
            ['code' => 'UNF-L-SCB-02-03'],
            ['name' => 'Uniform Scrub Laki - Laki STIKES', 'category_id' => $categories['Scrub Suit']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-L-SCB-02-03'], ['item_id' => $UNF_L_SCB_02->id, 'size' => '03', 'size_label' => 'S']);

        // Uniforms Perempuan
        $UNF_P_CLC_01 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLC-01-05'],
            ['name' => 'Uniform Clinical Perempuan Horizon', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLC-01-05'], ['item_id' => $UNF_P_CLC_01->id, 'size' => '05', 'size_label' => 'L']);

        $UNF_P_CLC_02 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLC-02-03'],
            ['name' => 'Uniform Clinical Perempuan STIKES', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLC-02-03'], ['item_id' => $UNF_P_CLC_02->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLC_03 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLC-03-03'],
            ['name' => 'Uniform Clinical Perempuan STMIK', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLC-03-03'], ['item_id' => $UNF_P_CLC_03->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLC_14 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLC-14-03'],
            ['name' => 'Uniform Clinical Perempuan S1 Pariwisata', 'category_id' => $categories['Seragam Praktek']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLC-14-03'], ['item_id' => $UNF_P_CLC_14->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLG_01 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLG-01-03'],
            ['name' => 'Uniform College Perempuan Horizon', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLG-01-03'], ['item_id' => $UNF_P_CLG_01->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLG_02 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLG-02-03'],
            ['name' => 'Uniform College Perempuan STIKES', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLG-02-03'], ['item_id' => $UNF_P_CLG_02->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLG_03 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLG-03-03'],
            ['name' => 'Uniform College Perempuan STMIK', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLG-03-03'], ['item_id' => $UNF_P_CLG_03->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_CLG_04 = Item::firstOrCreate(
            ['code' => 'UNF-P-CLG-04-03'],
            ['name' => 'Uniform College Perempuan STIE', 'category_id' => $categories['Seragam Kuliah']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-CLG-04-03'], ['item_id' => $UNF_P_CLG_04->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_COM_05 = Item::firstOrCreate(
            ['code' => 'UNF-P-COM-05-03'],
            ['name' => 'Uniform Community Perempuan S1 KEP', 'category_id' => $categories['Seragam Komunitas']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-COM-05-03'], ['item_id' => $UNF_P_COM_05->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_COM_06 = Item::firstOrCreate(
            ['code' => 'UNF-P-COM-06-03'],
            ['name' => 'Uniform Community Perempuan D3 KEP', 'category_id' => $categories['Seragam Komunitas']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-COM-06-03'], ['item_id' => $UNF_P_COM_06->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_COM_07 = Item::firstOrCreate(
            ['code' => 'UNF-P-COM-07-03'],
            ['name' => 'Uniform Community Perempuan D3 KEB', 'category_id' => $categories['Seragam Komunitas']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-COM-07-03'], ['item_id' => $UNF_P_COM_07->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_P_SCB_02 = Item::firstOrCreate(
            ['code' => 'UNF-P-SCB-02-03'],
            ['name' => 'Uniform Scrub Perempuan STIKES', 'category_id' => $categories['Scrub Suit']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-P-SCB-02-03'], ['item_id' => $UNF_P_SCB_02->id, 'size' => '03', 'size_label' => 'S']);

        // Uniforms Unisex
        $UNF_U_ALM_01 = Item::firstOrCreate(
            ['code' => 'UNF-U-ALM-01-03'],
            ['name' => 'Uniform Almamater Unisex Horizon', 'category_id' => $categories['Almamater']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-U-ALM-01-03'], ['item_id' => $UNF_U_ALM_01->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_U_ALM_02 = Item::firstOrCreate(
            ['code' => 'UNF-U-ALM-02-03'],
            ['name' => 'Uniform Almamater Unisex STIKES', 'category_id' => $categories['Almamater']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-U-ALM-02-03'], ['item_id' => $UNF_U_ALM_02->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_U_ALM_03 = Item::firstOrCreate(
            ['code' => 'UNF-U-ALM-03-03'],
            ['name' => 'Uniform Almamater Unisex STMIK', 'category_id' => $categories['Almamater']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-U-ALM-03-03'], ['item_id' => $UNF_U_ALM_03->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_U_ALM_04 = Item::firstOrCreate(
            ['code' => 'UNF-U-ALM-04-03'],
            ['name' => 'Uniform Almamater Unisex STIE', 'category_id' => $categories['Almamater']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-U-ALM-04-03'], ['item_id' => $UNF_U_ALM_04->id, 'size' => '03', 'size_label' => 'S']);

        $UNF_U_LAB_02 = Item::firstOrCreate(
            ['code' => 'UNF-U-LAB-02-03'],
            ['name' => 'Uniform Laboratory Unisex STIKES', 'category_id' => $categories['Jas Lab']->id, 'unit' => 'pcs', 'selling_price' => 0, 'hpp' => 0]
        );
        ItemVariant::firstOrCreate(['sku' => 'UNF-U-LAB-02-03'], ['item_id' => $UNF_U_LAB_02->id, 'size' => '03', 'size_label' => 'S']);

        // Total: 43 items, 43 variants
    }
}
