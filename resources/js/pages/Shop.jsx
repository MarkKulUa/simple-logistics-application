import React from 'react';
import Suppliers from "../components/Suppliers/Suppliers";
import Warehouses from "../components/Warehouses/Warehouses";
import Products from "../components/Products/Products";

const Shop = () => {
    return (
        <>
            <div className="text-6xl font-bold text-slate-600">Warehouse</div>
            <hr className="bg-slate-400 h-1 w-full my-4"/>
            <Suppliers />
            <Warehouses />
            <Products />
        </>
    );
}

export default Shop;
