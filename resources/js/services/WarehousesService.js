import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

export const warehouseAPI = createApi({
  reducerPath: 'warehouseAPI',
  baseQuery: fetchBaseQuery({
    baseUrl: import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  }),
  tagTypes: ['Warehouse'],
  endpoints: (build) => ({
    fetchAllWarehouses: build.query({
      query: (limit = 5) => ({
        url: '/warehouses',
        params: { _limit: limit }
      }),
      providesTags: (result) => ['Warehouse']
    }),
    createWarehouses: build.mutation({
      query: (warehouse) => ({
        url: '/warehouses',
        method: 'POST',
        body: warehouse
      }),
      invalidatesTags: ['Warehouse']
    }),
    updateWarehouse: build.mutation({
      query: (warehouse) => ({
        url: `/warehouses/${warehouse.id}`,
        method: 'PUT',
        body: warehouse
      }),
      invalidatesTags: ['Warehouse']
    }),
    deleteWarehouse: build.mutation({
      query: (warehouse) => ({
        url: `/warehouses/${warehouse.id}`,
        method: 'DELETE'
      }),
      invalidatesTags: ['Warehouse']
    })
  })
})
