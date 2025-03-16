import { createSlice, isAnyOf } from '@reduxjs/toolkit'
import {
  changeSupplierPerPage,
  createSupplier,
  deleteSupplier,
  fetchSuppliers,
  updateSupplier
} from './SuppliersActions'

const getErrorMessage = (payload) => {
  if (typeof payload === 'string') {
    return payload
  }
  return 'An error occurred'
}

const initialState = {
  suppliers: [],
  isLoading: false,
  error: '',
  suppliersPerPage: 5
}

export const supplierSlice = createSlice({
  name: 'suppliers',
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchSuppliers.fulfilled, (state, { payload }) => {
        state.suppliers = payload || []
      })
      .addCase(createSupplier.fulfilled, (state, { payload }) => {
        state.suppliers = [...state.suppliers, payload]
      })
      .addCase(deleteSupplier.fulfilled, (state, { payload }) => {
        state.suppliers = state.suppliers.filter((supplier) => supplier.id !== payload.id)
      })
      .addCase(updateSupplier.fulfilled, (state, { payload }) => {
        state.suppliers = state.suppliers.map((supplier) =>
          supplier.id === payload.id ? payload : supplier
        )
      })
      .addCase(changeSupplierPerPage.fulfilled, (state, { payload }) => {
        state.suppliersPerPage = payload
      })
      .addMatcher(
        isAnyOf(fetchSuppliers.pending, createSupplier.pending, deleteSupplier.pending),
        (state) => {
          state.isLoading = true
          state.error = ''
        }
      )
      .addMatcher(
        isAnyOf(
          fetchSuppliers.fulfilled,
          createSupplier.fulfilled,
          deleteSupplier.fulfilled
        ),
        (state) => {
          state.isLoading = false
          state.error = ''
        }
      )
      .addMatcher(
        isAnyOf(fetchSuppliers.rejected, createSupplier.rejected, deleteSupplier.rejected),
        (state, { payload }) => {
          state.isLoading = false
          state.error = getErrorMessage(payload)
        }
      )
  }
})

export default supplierSlice.reducer
