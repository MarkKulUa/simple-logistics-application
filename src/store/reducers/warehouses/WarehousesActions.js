import axios from 'axios'

import { createAsyncThunk } from '@reduxjs/toolkit'

const host = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api')
export const fetchWarehouses = createAsyncThunk(
  'warehouses/fetchWarehouses',
  async (limit, thunkAPI) => {
    try {
      const response = await axios.get(
        host + `/warehouses?_limit=${limit}`
      )
      return response.data
    } catch (error) {
      return thunkAPI.rejectWithValue('No warehouses found')
    }
  }
)

export const createWarehouse = createAsyncThunk(
  'warehouses/createWarehouse',
  async (newWarehouse, thunkAPI) => {
    try {
      const response = await axios.warehouse(
        host + '/warehouses',
        newWarehouse
      )
      return response.data
    } catch (error) {
      return thunkAPI.rejectWithValue('No warehouses added')
    }
  }
)

export const updateWarehouse = createAsyncThunk(
  'warehouses/updateWarehouse',
  async (warehouse, thunkAPI) => {
    try {
      const { data } = await axios.put(
        `${host}/warehouses/${warehouse.id}`,
        warehouse
      )
      return { ...warehouse, ...data }
    } catch (error) {
      return thunkAPI.rejectWithValue('No warehouses updated')
    }
  }
)
export const deleteWarehouse = createAsyncThunk(
  'warehouses/deleteWarehouse',
  async (warehouse, thunkAPI) => {
    try {
      const { data } = await axios.delete(
        `${host}/warehouses/${warehouse.id}`
      )
      return { id: warehouse.id, data }
    } catch (error) {
      return thunkAPI.rejectWithValue('No warehouses deleted')
    }
  }
)
export const changeWarehousePerPage = createAsyncThunk(
  'warehouses/changeWarehousePerPage',
  async (limit, thunkAPI) => {
    try {
      return limit
    } catch (error) {
      return thunkAPI.rejectWithValue('No warehouses deleted')
    }
  }
)
