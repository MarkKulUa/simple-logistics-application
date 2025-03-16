import { configureStore, combineReducers } from '@reduxjs/toolkit'
import SupplierReducer from './reducers/suppliers/SuppliersSlice'
import WarehouseReducer from './reducers/warehouses/WarehousesSlice'
import ProductReducer from './reducers/products/ProductsSlice'
import { supplierAPI } from '../services/SuppliersService'
import { warehouseAPI } from '../services/WarehousesService'
import { productAPI } from '../services/ProductsService'

const rootReducer = combineReducers({
  SupplierReducer,
  WarehouseReducer,
  ProductReducer,
  [supplierAPI.reducerPath]: supplierAPI.reducer,
  [warehouseAPI.reducerPath]: warehouseAPI.reducer,
  [productAPI.reducerPath]: productAPI.reducer
})

export const setupStore = () => {
  return configureStore({
    reducer: rootReducer,
    middleware: (getDefaultMiddleware) =>
      getDefaultMiddleware().concat(
        supplierAPI.middleware,
        warehouseAPI.middleware,
        productAPI.middleware
      )
  })
}
