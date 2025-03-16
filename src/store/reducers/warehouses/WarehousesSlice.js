import { createSlice, isAnyOf } from '@reduxjs/toolkit'
import {
  changeWarehousePerPage,
  createWarehouse,
  deleteWarehouse,
  fetchWarehouses,
  updateWarehouse
} from './WarehousesActions'

const getErrorMessage = (payload) => {
  if (typeof payload === 'string') {
    return payload
  }
  return 'An error occurred'
}

const initialState = {
  warehouses: [],
  isLoading: false,
  error: '',
  warehousesPerPage: 5
}

export const warehouseSlice = createSlice({
  name: 'warehouses',
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchWarehouses.fulfilled, (state, { payload }) => {
        state.warehouses = payload || []
      })
      .addCase(createWarehouse.fulfilled, (state, { payload }) => {
        state.warehouses = [...state.warehouses, payload]
      })
      .addCase(deleteWarehouse.fulfilled, (state, { payload }) => {
        state.warehouses = state.warehouses.filter((warehouse) => warehouse.id !== payload.id)
      })
      .addCase(updateWarehouse.fulfilled, (state, { payload }) => {
        state.warehouses = state.warehouses.map((warehouse) =>
          warehouse.id === payload.id ? payload : warehouse
        )
      })
      .addCase(changeWarehousePerPage.fulfilled, (state, { payload }) => {
        state.warehousesPerPage = payload
      })
      .addMatcher(
        isAnyOf(fetchWarehouses.pending, createWarehouse.pending, deleteWarehouse.pending),
        (state) => {
          state.isLoading = true
          state.error = ''
        }
      )
      .addMatcher(
        isAnyOf(
          fetchWarehouses.fulfilled,
          createWarehouse.fulfilled,
          deleteWarehouse.fulfilled
        ),
        (state) => {
          state.isLoading = false
          state.error = ''
        }
      )
      .addMatcher(
        isAnyOf(fetchWarehouses.rejected, createWarehouse.rejected, deleteWarehouse.rejected),
        (state, { payload }) => {
          state.isLoading = false
          state.error = getErrorMessage(payload)
        }
      )
  }
})

export default warehouseSlice.reducer
