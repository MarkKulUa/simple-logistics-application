import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";

export const supplierAPI = createApi({
  reducerPath: "supplierAPI",
  baseQuery: fetchBaseQuery({
    baseUrl: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  }),
  tagTypes: ["Supplier"],
  endpoints: (build) => ({
    fetchAllSuppliers: build.query({
      query: (limit = 5) => ({
        url: "/suppliers",
        params: { _limit: limit },
      }),
      providesTags: (result) => ["Supplier"],
    }),
    createSuppliers: build.mutation({
      query: (supplier) => ({
        url: "/suppliers",
        method: "POST",
        body: supplier,
      }),
      invalidatesTags: ["Supplier"],
    }),
    updateSupplier: build.mutation({
      query: (supplier) => ({
        url: `/suppliers/${supplier.id}`,
        method: "PUT",
        body: supplier,
      }),
      invalidatesTags: ["Supplier"],
    }),
    deleteSupplier: build.mutation({
      query: (supplier) => ({
        url: `/suppliers/${supplier.id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Supplier"],
    }),
  }),
});
