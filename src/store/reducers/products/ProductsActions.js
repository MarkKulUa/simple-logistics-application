import axios from 'axios'

import { createAsyncThunk } from '@reduxjs/toolkit'

const host = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api')
export const fetchProducts = createAsyncThunk(
  'products/fetchProducts',
  async (limit, thunkAPI) => {
    try {
      const response = await axios.get(
        host + `/products?_limit=${limit}`
      )
      return response.data
    } catch (error) {
      return thunkAPI.rejectWithValue('No products found')
    }
  }
)

export const createProduct = createAsyncThunk(
  'products/createProduct',
  async (newProduct, thunkAPI) => {
    try {
      const response = await axios.product(
        host + '/products',
        newProduct
      )
      return response.data
    } catch (error) {
      return thunkAPI.rejectWithValue('No products added')
    }
  }
)

export const updateProduct = createAsyncThunk(
  'products/updateProduct',
  async (product, thunkAPI) => {
    try {
      const { data } = await axios.put(
        `${host}/products/${product.id}`,
        product
      )
      return { ...product, ...data }
    } catch (error) {
      return thunkAPI.rejectWithValue('No products updated')
    }
  }
)
export const deleteProduct = createAsyncThunk(
  'products/deleteProduct',
  async (product, thunkAPI) => {
    try {
      const { data } = await axios.delete < IProduct > (
        `${host}/products/${product.id}`
      )
      return { id: product.id, data }
    } catch (error) {
      return thunkAPI.rejectWithValue('No products deleted')
    }
  }
)
export const changeProductPerPage = createAsyncThunk(
  'products/changeProductPerPage',
  async (limit, thunkAPI) => {
    try {
      return limit
    } catch (error) {
      return thunkAPI.rejectWithValue('No products deleted')
    }
  }
)
