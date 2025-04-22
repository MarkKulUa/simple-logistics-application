import React from "react";
import cl from "./style.module.css";
import MyButton from "../UI/button/MyButton";

const Supplier = ({ data, remove, update }) => {
  return (
    <li className={cl.item}>
      <div>
          <strong>
              #{data.id} - {data.name}
          </strong>
          <p>{data.address}</p>
      </div>
      {/*<div className={cl.btnList}>*/}
      {/*  <MyButton onClick={() => update(data)}>Update</MyButton>*/}
      {/*  <MyButton onClick={() => remove(data)}>Delete</MyButton>*/}
      {/*</div>*/}
    </li>
  );
};

export default Supplier;
