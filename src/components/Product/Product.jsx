import React from "react";
import cl from "./style.module.css";

const Product = ({ data }) => {
  return (
    <li className={cl.item}>
      <div>
          <strong>
              #{data.id} - {data.name}
          </strong>
          <p>{data.supplier_id}</p>
          <p>{data.price}</p>
      </div>
    </li>
  );
};

export default Product;
