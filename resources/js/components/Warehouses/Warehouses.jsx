import React, { useEffect, useState } from "react";
import { warehouseAPI } from "../../services/WarehousesService";
import Warehouse from "../Warehouse/Warehouse";
import style from "./style.module.css";
import Loader from "../UI/Loader/Loader";
import MyButton from "../UI/button/MyButton";
import { useAppSelector } from "../../hooks/redux";
import arraysAreEqual from "../../services/arraysEqual";

const Warehouses = () => {
  const { warehouses: AxiosWarehouses } = useAppSelector((state) => state.WarehouseReducer);

  const [limit, setLimit] = useState(15);

  const {
    data: warehouses,
    isLoading,
    error,
    refetch,
  } = warehouseAPI.useFetchAllWarehousesQuery(limit);

  const [createWarehouse, {}] = warehouseAPI.useCreateWarehousesMutation();
  const [updateWarehouse, {}] = warehouseAPI.useUpdateWarehouseMutation();
  const [removeWarehouse, {}] = warehouseAPI.useDeleteWarehouseMutation();

  useEffect(() => {
    if (AxiosWarehouses && warehouses) {
      const equal = arraysAreEqual(warehouses, AxiosWarehouses);
      !equal && refetch();
    }
  }, [AxiosWarehouses]);

  const handleCreateWarehouse = () => {
    const name = window.prompt("Enter warehouse name");
    const address = window.prompt("Enter warehouse address");
    const supplier_id = window.prompt("Enter supplier_id");

    const newWarehouse = { name, address, supplier_id };

    if (name && address) {
      createWarehouse(newWarehouse);
    } else alert("All fields must be created");
  };

  const handleRemove = (warehouse) => {
    removeWarehouse(warehouse);
  };

  const handleUpdate = (warehouse) => {
    const name = window.prompt("Enter warehouse name", warehouse.name);
    const address = window.prompt("Enter warehouse address", warehouse.address);
    const supplier_id = window.prompt("Enter supplier_id", warehouse.supplier_id);

    updateWarehouse({ ...warehouse, name, address, supplier_id });
  };

  return (
    <div className={style.container}>
      <h2 className={style.heading}>Warehouses</h2>
      <div className={style.btnList}>
        <MyButton
          additionalClassName={style.createBtn}
          onClick={() => handleCreateWarehouse()}
        >
          Create Warehouse
        </MyButton>
        <MyButton additionalClassName={style.refetchBtn} onClick={() => refetch()}>
          Refetch
        </MyButton>
        <select
          onChange={(e) =>
            setLimit(parseInt(e.target.value))
          }
        >
          <option value={15}>Default</option>
          <option value={2}>2</option>
          <option value={20}>20</option>
        </select>
      </div>
      <ul>
        {isLoading && <Loader />}
        {error && <h3>Error acquired</h3>}
        {warehouses && warehouses.data &&
          warehouses.data.map((warehouse) => (
            <Warehouse
              key={warehouse.id}
              data={warehouse}
              update={handleUpdate}
              remove={handleRemove}
            />
          ))}
      </ul>
    </div>
  );
};

export default Warehouses;
