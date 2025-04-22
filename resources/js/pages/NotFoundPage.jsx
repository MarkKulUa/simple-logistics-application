import React from 'react';
import Shop from "./Shop";
import Spinner from "../components/Spinner/Spinner";

const NotFoundPage = () => {
    return (
        <>
            <div className="text-6xl font-bold text-slate-600">Page Not Found</div>
            <hr className="bg-slate-400 h-1 w-full my-4" />
            <p>
               404
            </p>
        </>
    );
}

export default NotFoundPage;
