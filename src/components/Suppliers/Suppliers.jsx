import React, { FC, useEffect, useState } from "react";
import { supplierAPI } from "../../services/SuppliersService";
import Supplier from "../Supplier/Supplier";
import style from "./style.module.css";
import Loader from "../UI/Loader/Loader";
import MyButton from "../UI/button/MyButton";
import { useAppSelector } from "../../hooks/redux";
import arraysAreEqual from "../../services/arraysEqual";

const Suppliers = () => {
  const { suppliers: AxiosSuppliers } = useAppSelector((state) => state.SupplierReducer);

  const [limit, setLimit] = useState(15);

  const {
    data: suppliers,
    isLoading,
    error,
    refetch,
  } = supplierAPI.useFetchAllSuppliersQuery(limit);

  const [createSupplier, {}] = supplierAPI.useCreateSuppliersMutation();
  const [updateSupplier, {}] = supplierAPI.useUpdateSupplierMutation();
  const [removeSupplier, {}] = supplierAPI.useDeleteSupplierMutation();

  useEffect(() => {
    if (AxiosSuppliers && suppliers) {
      const equal = arraysAreEqual(suppliers, AxiosSuppliers);
      !equal && refetch();
    }
  }, [AxiosSuppliers]);

  const handleCreateSupplier = () => {
    const name = window.prompt("Enter supplier name");
    const address = window.prompt("Enter supplier address");

    const newSupplier = { name, address };

    if (name && address) {
      createSupplier(newSupplier);
    } else alert("All fields must be created");
  };

  const handleRemove = (supplier) => {
    removeSupplier(supplier);
  };

  const handleUpdate = (supplier) => {
    const name = window.prompt("Enter supplier name", supplier.name);
    const address = window.prompt("Enter supplier address", supplier.address);

    updateSupplier({ ...supplier, name, address });
  };

  return (
    <div className={style.container}>
      <h2 className={style.heading}>Suppliers</h2>
      <div className={style.btnList}>
        <MyButton
          additionalClassName={style.createBtn}
          onClick={() => handleCreateSupplier()}
        >
          Create Supplier
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
        {suppliers && suppliers.data &&
          suppliers.data.map((supplier) => (
            <Supplier
              key={supplier.id}
              data={supplier}
              update={handleUpdate}
              remove={handleRemove}
            />
          ))}
      </ul>
    </div>
  );
};

export default Suppliers;
