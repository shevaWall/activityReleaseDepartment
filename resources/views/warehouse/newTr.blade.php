<tr data-warehouse-item="{{(isset($warehouse_item->id)) ? $warehouse_item->id : 0}}">
    <td class="materialName">
        <span class="warehouse-text">{{(isset($warehouse_item->material)) ?$warehouse_item->material : ''}}</span>
        <input class="d-none col-12 materialNameInput"
               type="text"
               value="{{(isset($warehouse_item->material)) ? $warehouse_item->material : ''}}"
               placeholder="Название материала">
    </td>
    <td class="materialQuantity">
        <span class="warehouse-text">{{(isset($warehouse_item->quantity)) ? $warehouse_item->quantity : ''}}</span>
        <input class="d-none col-12 materialQuantityInput"
               type="number"
               value="{{(isset($warehouse_item->quantity)) ? $warehouse_item->quantity : ''}}"
               placeholder="Количество">
    </td>
    <td class="text-center">
        <img class="editPencil"
             style="width:30px;"
             src="/images/pencil.svg"
             alt="Редактировать"
             onclick="editWarehouseItem(this)"
        >
        <img class="editPencil"
             style="width:30px;"
             src="/images/delete.svg"
             alt="Удалить"
             onclick="deleteWarehouseItem(this)"
        >
    </td>
</tr>
