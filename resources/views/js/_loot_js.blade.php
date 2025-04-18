@php
if(!isset($showRecipes)) $showRecipes = false;
@endphp
<script>
$( document ).ready(function() {    
    var $lootTable  = $('#lootTableBody');
    var $lootRow = $('#lootRow').find('.loot-row');
    var $itemSelect = $('#lootRowData').find('.item-select');
    var $PetSelect = $('#lootRowData').find('.pet-select');
    var $WeaponSelect = $('#lootRowData').find('.weapon-select');
    var $GearSelect = $('#lootRowData').find('.gear-select');
    var $ElementSelect = $('#lootRowData').find('.element-select');
    var $currencySelect = $('#lootRowData').find('.currency-select');
    var $awardSelect = $('#lootRowData').find('.award-select');
    @if($showLootTables)
        var $tableSelect = $('#lootRowData').find('.table-select');
    @endif
    @if($showRaffles)
        var $raffleSelect = $('#lootRowData').find('.raffle-select');
    @endif
    @if($showRecipes)
        var $recipeSelect = $('#lootRowData').find('.recipe-select');
    @endif

    @if(isset($showBorders) && $showBorders)
        var $borderSelect = $('#lootRowData').find('.border-select');
    @endif

    $('#lootTableBody .selectize').selectize();
    attachRemoveListener($('#lootTableBody .remove-loot-button'));

    $('#addLoot').on('click', function(e) {
        e.preventDefault();
        var $clone = $lootRow.clone();
        $lootTable.append($clone);
        attachRewardTypeListener($clone.find('.reward-type'));
        attachRemoveListener($clone.find('.remove-loot-button'));
    });

    $('.reward-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.loot-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'Award') $clone = $awardSelect.clone();
        else if (val == 'Pet') $clone = $PetSelect.clone();
        else if (val == 'Weapon') $clone = $WeaponSelect.clone();
        else if (val == 'Element') $clone = $ElementSelect.clone();
        else if (val == 'Gear') $clone = $GearSelect.clone();
        @if($showLootTables)
            else if (val == 'LootTable') $clone = $tableSelect.clone();
        @endif
        @if ($showRaffles)
            else if (val == 'Raffle') $clone = $raffleSelect.clone();
        @endif
        @if($showRecipes)
            else if (val == 'Recipe') $clone = $recipeSelect.clone();
        @endif

        @if(isset($showBorders) && $showBorders)
            else if (val == 'Border') $clone = $borderSelect.clone();
        @endif
            $cell.html('');
            $cell.append($clone);
    });
    

        function attachRewardTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.loot-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Pet') $clone = $PetSelect.clone();
                else if (val == 'Currency') $clone = $currencySelect.clone();
                else if (val == 'Weapon') $clone = $WeaponSelect.clone();
                else if (val == 'Gear') $clone = $GearSelect.clone();
                else if (val == 'Element') $clone = $ElementSelect.clone();
                else if (val == 'Award') $clone = $awardSelect.clone();
                @if ($showLootTables)
                    else if (val == 'LootTable') $clone = $tableSelect.clone();
                @endif
                @if ($showRaffles)
                    else if (val == 'Raffle') $clone = $raffleSelect.clone();
                @endif
                @if($showRecipes)
                else if (val == 'Recipe') $clone = $recipeSelect.clone();
                @endif
                @if(isset($showBorders) && $showBorders)
                else if (val == 'Border') $clone = $borderSelect.clone();
                @endif

                $cell.html('');
                $cell.append($clone);
                $clone.selectize();
            });
        }

        function attachRemoveListener(node) {
            node.on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        }
});


</script>
