import React, { useEffect, useState } from "react";
import { productAPI } from "../../services/ProductsService";
import Product from "../Product/Product";
import style from "./style.module.css";
import Loader from "../UI/Loader/Loader";
import MyButton from "../UI/button/MyButton";
import { useAppSelector } from "../../hooks/redux";
import arraysAreEqual from "../../services/arraysEqual";

const Products = () => {
  const { products: AxiosProducts } = useAppSelector((state) => state.ProductReducer);

  const [limit, setLimit] = useState(15);

  const {
    data: products,
    isLoading,
    error,
    refetch,
  } = productAPI.useFetchAllProductsQuery(limit);

  const [createProduct, {}] = productAPI.useCreateProductsMutation();
  const [updateProduct, {}] = productAPI.useUpdateProductMutation();
  const [removeProduct, {}] = productAPI.useDeleteProductMutation();

  useEffect(() => {
    if (AxiosProducts && products) {
      const equal = arraysAreEqual(products, AxiosProducts);
      !equal && refetch();
    }
  }, [AxiosProducts]);

  const handleCreateProduct = () => {
    const name = window.prompt("Enter product name");
    const price = window.prompt("Enter product price");
    const supplier_id = window.prompt("Enter supplier_id");
    const warehouse_id = window.prompt("Enter warehouse_id");
    const quantity_in_stock = window.prompt("Enter quantity_in_stock");

    const newProduct = { name, price, supplier_id, warehouse_id, quantity_in_stock };

    if (name && price) {
      createProduct(newProduct);
    } else alert("All fields must be created");
  };

  const handleRemove = (product) => {
    removeProduct(product);
  };

  const handleUpdate = (product) => {
    const name = window.prompt("Enter product name", product.name);
    const price = window.prompt("Enter product price");
    const supplier_id = window.prompt("Enter supplier_id");
    const warehouse_id = window.prompt("Enter warehouse_id");
    const quantity_in_stock = window.prompt("Enter quantity_in_stock");

    updateProduct({ ...product, name, price, supplier_id, warehouse_id, quantity_in_stock });
  };

  return (
    <div className={style.container}>
      <h2 className={style.heading}>Products</h2>
      <div className={style.btnList}>
        <MyButton
          additionalClassName={style.createBtn}
          onClick={() => handleCreateProduct()}
        >
          Create Product
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
        {products && products.data &&
          products.data.map((product) => (
            <Product
              key={product.id}
              data={product}
              update={handleUpdate}
              remove={handleRemove}
            />
          ))}
      </ul>
    </div>
  );
};

export default Products;
