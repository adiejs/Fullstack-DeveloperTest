// src/products/dto/create-product.dto.ts
import { IsString, IsNotEmpty, IsNumber, IsOptional, IsUrl, Min } from 'class-validator';

export class CreateProductDto {
  @IsString()
  @IsNotEmpty()
  name: string;

  @IsString()
  @IsOptional()
  description?: string;

  @IsNumber()
  @Min(0)
  price: number;

  @IsString()
  @IsOptional()
  @IsUrl({ require_tld: false }) // Validate if it's a URL
  imageUrl?: string; // Tambahkan ini
}