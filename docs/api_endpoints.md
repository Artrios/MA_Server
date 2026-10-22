# MA Server API

_Version: 1.0.0_

OpenAPI documentation for PoryNET. The `/pokemonrse` endpoints handle communication with the Game Boy Advance client.

## Table of Contents

- [`GET` /api/gts](#get-apigts)
- [`GET` /pokemonrse/worldexchange/info](#get-pokemonrseworldexchangeinfo)
- [`GET` /pokemonrse/worldexchange/result](#get-pokemonrseworldexchangeresult)
- [`GET` /pokemonrse/worldexchange/get](#get-pokemonrseworldexchangeget)
- [`GET` /pokemonrse/worldexchange/delete](#get-pokemonrseworldexchangedelete)
- [`GET` /pokemonrse/worldexchange/return](#get-pokemonrseworldexchangereturn)
- [`GET` /pokemonrse/worldexchange/post](#get-pokemonrseworldexchangepost)
- [`GET` /pokemonrse/worldexchange/post_finish](#get-pokemonrseworldexchangepost-finish)
- [`GET` /pokemonrse/worldexchange/search](#get-pokemonrseworldexchangesearch)
- [`GET` /pokemonrse/worldexchange/exchange](#get-pokemonrseworldexchangeexchange)
- [`GET` /pokemonrse/worldexchange/exchange_finish](#get-pokemonrseworldexchangeexchange-finish)
- [`GET` /pokemonrse/common/createprofile](#get-pokemonrsecommoncreateprofile)
- [`GET` /pokemonrse/common/setprofile](#get-pokemonrsecommonsetprofile)


## Endpoints

### `/api/gts`

#### GET

**Operation ID:** `GET-/api/gts`
**Summary:** Get Pokémon GTS listings

Returns up to 20 Pokémon listings in JSON format for the web frontend.

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | GTS listings. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/info`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/info`
**Summary:** Check server availability

Returns immediately without performing the token authentication.

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Server is online. The implementation returns the two-byte big-endian value `0x0001`. |

**Response Examples — `200`**

*Media type:* `application/octet-stream`

```
0x0001
```

### `/pokemonrse/worldexchange/result`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/result`
**Summary:** Check whether a deposited Pokémon was traded

If the Pokémon has been exchanged, returns a 124-byte GTS record. Otherwise returns a two-byte status: `0x0004` if not traded or `0x0005` if the player has no deposited Pokémon.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | If the Pokémon has been exchanged, returns a 124-byte Pokémon record. Otherwise returns a two-byte status: `0x0004` if not traded or `0x0005` if the player has no deposited Pokémon. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/get`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/get`
**Summary:** Retrieve deposited Pokémon data

Returns the raw 80-byte Pokémon payload when the requested PID exists. If it does not exist, returns the two-byte status `0x0001`.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Returns the raw 80-byte Pokémon payload when the requested PID exists. If it does not exist, returns the two-byte status `0x0001`. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/delete`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/delete`
**Summary:** Delete a traded Pokémon

Deletes the deposited Pokémon after it has been retrieved. Returns `0x0001` on success. If the Pokémon does not exist, the implementation returns HTTP 401.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Deletes the deposited Pokémon after it has been retrieved. Returns `0x0001` on success. If the Pokémon does not exist, the implementation returns HTTP 401. |
| `401` | The requested Pokémon was not found (the implementation uses HTTP 401 for this case). |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/return`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/return`
**Summary:** Return/delete a deposited Pokémon

Deletes the deposited Pokémon if it exists as it has been withdrawn from the GTS. Returns `0x0001` regardless of whether the Pokémon existed.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Deletes the deposited Pokémon if it exists as it has been withdrawn from the GTS. Returns `0x0001` regardless of whether the Pokémon existed. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/post`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/post`
**Summary:** Deposit a Pokémon

Decrypts and validates the supplied Pokémon packet, stores it as `DEPOSITED_AWAITING_CONFIRMATION`, and returns `0x0001`.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |
| `data` | query | no | object | URL-safe Base64 encrypted Pokémon packet. The decrypted packet is 121 bytes. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Decrypts and validates the supplied Pokémon packet, stores it as `DEPOSITED_AWAITING_CONFIRMATION`, and returns `0x0001`. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/post_finish`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/post_finish`
**Summary:** Confirm a deposited Pokémon

 Marks the Pokémon identified by `pid` as deposited. Returns `0x0001` when found and updated, or `0x0002` when it is not found.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Marks the Pokémon identified by `pid` as deposited. Returns `0x0001` when found and updated, or `0x0002` when it is not found. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/search`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/search`
**Summary:** Search for Pokémon in the GTS

Decrypts the search request and returns zero or more 124-byte Pokémon records. If no matching Pokémon is found, returns the two-byte status `0x0001`.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |
| `data` | query | yes | object | URL-safe Base64 encrypted search packet. The decrypted payload contains checksum, species, gender, minimum level, maximum level, country and offset. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Decrypts the search request and returns zero or more 124-byte Pokémon records. If no matching Pokémon is found, returns the two-byte status `0x0001`. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/exchange`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/exchange`
**Summary:** Attempt to trade a Pokémon

Decrypts and validates a Pokémon packet and stores it as `DEPOSITED_AWAITING_CONFIRMATION`. The current implementation returns `0x0001` after storing the packet.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |
| `data` | query | yes | object | URL-safe Base64 encrypted Pokémon packet. The decrypted packet is expected to contain the same 121-byte Pokémon structure used by `/post/`. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Decrypts and validates a Pokémon packet and stores it as `DEPOSITED_AWAITING_CONFIRMATION`. The current implementation returns `0x0001` after storing the packet. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/worldexchange/exchange_finish`

#### GET

**Operation ID:** `GET-/pokemonrse/worldexchange/exchange_finish`
**Summary:** Confirm a Pokémon trade

Completes the exchange between the newly deposited Pokémon (`pid`) and the old deposited Pokémon (`data`). Returns a two-byte status code.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the 32-character token returned by the preceding authentication request. |
| `data` | query | yes | string | PID of the old deposited Pokémon. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Completes the exchange between the newly deposited Pokémon (`pid`) and the old deposited Pokémon (`data`). Returns a two-byte status code. Status codes: `0x0001` success; `0x0002` old Pokémon not found; `0x0003` old Pokémon is not available; `0x0004` new Pokémon not found. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/common/createprofile`

#### GET

**Operation ID:** `GET-/pokemonrse/common/createprofile`
**Summary:** Create a client profile

Creates a profile for the supplied trainer ID and returns the generated profile database ID as a four-byte big-endian integer.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the authentication token. |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Creates a profile for the supplied trainer ID and returns the generated profile database ID as a four-byte big-endian integer. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

### `/pokemonrse/common/setprofile`

#### GET

**Operation ID:** `GET-/pokemonrse/common/setprofile`
**Summary:** Update a client profile

Decrypts and validates the profile packet and updates the stored profile. The decrypted profile payload is 100 bytes.

**Parameters:**

| Name | In | Required | Type | Description |
| ---- | -- | -------- | ---- | ----------- |
| `pid` | query | yes | string | PID supplied by the GBA client. |
| `hash` | query | no | string | SHA-1 session hash generated from the authentication token. |
| `data` | query | yes | string |  |

**Responses:**

| Code | Description |
| ---- | ----------- |
| `200` | Decrypts and validates the profile packet and updates the stored profile. The decrypted profile payload is 100 bytes. |
| `401` | Authentication or request validation failed. |

**Response Examples — `200`**

## Security

| Scheme | Type | In | Name | Description |
| ------ | ---- | -- | ---- | ----------- |
| `authHash` | apiKey | query | hash | Authentication hash returned indirectly by the MA authentication handshake. The first request to most `/pokemonrse` endpoints (`/pokemonrse/worldexchange/info` is exempted) includes `pid` without `hash`. The server makes the session ID the `pid` value and returns a 32-character token for the game client to generate the SHA-1 hash of `sAdeqWo3voLeC5r16DYv` + token. The second request includes `hash` for the server to authenticate the session associated with that hash. |

## Schemas

### `GTSListing`

| Property | Type | Required | Description |
| -------- | ---- | -------- | ----------- |
| `speciesID` | string | yes | The Pokémon's species ID No., or `0` if it's invalid. |
| `species` | string | yes | The species name. |
| `nickname` | string | yes | The Pokémon's nickname. |
| `ball` | integer | yes | The Pokémon's ball. |
| `level` | integer | yes | The Pokémon's level. |
| `gender` | integer | yes | The Pokémon's gender. |
| `shiny` | boolean | yes | Is the Pokémon shiny. |
| `item` | string | yes | The Pokémon's held item. |
| `offerer` | string | yes | The trainer offering the Pokémon. |
| `offererGender` | integer | yes | The trainer's gender. |
| `OT` | string | yes | The Pokémon's original trainer. |
| `OTGender` | integer | yes | The Pokémon's original trainer's gender. |
| `wantedID` | string | yes | The wanted Pokémon species ID No., or `0` if it's invalid. |
| `wanted` | string | yes | The wanted Pokémon species name. |
| `wantedGender` | integer | yes | The wanted Pokémon's gender. |
| `wantedMinLevel` | integer | yes | The wanted Pokémon's minimum level. |
| `wantedMaxLevel` | integer | yes | The wanted Pokémon's maximum level. |
| `version` | integer | yes | The game version the Pokémon was deposited from. |
| `language` | integer | yes | The game language the Pokémon was deposited from. |

### `AuthTokenResponse`

32-character session token returned by the initial authentication request.

**Example:**

```
aB3xY7pQ2mN8kL4sR9tV1wX5zC6dE0fG
```

### `StatusCode`

Two-byte status code.

### `ProfileId`

The profile ID (PID) generated for clients.

### `ProfileUpdateResult`

Eight zero-valued bytes returned after setting the profile (any value returned is a success? To look into more).

### `BoxPokemonData`

80-byte encrypted Box Pokémon data.

### `GTSRecord`

| Property | Type | Required | Description |
| -------- | ---- | -------- | ----------- |
| `checksum` | integer | yes | Checksum for the record data. |
| `pid` | integer | yes | The profile ID (PID) of the player that uploaded the Pokémon. |
| `BoxPokemon` | object | yes | The 80-byte box Pokémon data. |
| `dexnum` | integer | yes | The species ID of the Pokémon. |
| `gender` | integer | yes | The gender of the Pokémon (`0x01`==MALE, `0xFE`==FEMALE, `0xFF`==GENDERLESS). |
| `level` | integer | yes | The Pokémon's level. |
| `natDexRequest` | integer | yes | The wanted Pokémon species ID No. |
| `genderRequest` | integer | yes | The wanted Pokémon's gender. |
| `minLevel` | integer | yes | The wanted Pokémon's minimum level. |
| `maxLevel` | integer | yes | The wanted Pokémon's maximum level. |
| `trainerGender` | integer | yes | The trainer's gender. |
| `trainerID` | integer | yes | The trainer's ID No. |
| `secretID` | integer | yes | The trainer's Secret ID No. |
| `OTName` | string | yes | The Pokémon's original trainer. |
| `country` | integer | yes | The trainer's country of origin. |
| `region` | integer | yes | The trainer's region of origin. |
| `trainerClass` | integer | yes | The trainer's class. |
| `isExchanged` | boolean | yes | Has the Pokémon been exchanged? |
| `gameVersion` | integer | yes | The game version the Pokémon was deposited from. |
| `romHackID` | integer | yes | The RomHack ID the Pokémon was deposited from. |
| `romHackVersion` | integer | yes | The RomHack Version the Pokémon was deposited from. |
| `language` | integer | yes | The game language the Pokémon was deposited from. |
| `padding` | null | yes | Padding to align the data to the game struct. |

### `PokemonSearchRequest`

URL-safe Base64 encoded encrypted search packet supplied through the `data` query parameter. After decryption the implementation reads a checksum followed by species, gender, minimum level, maximum level, country and offset.

### `PokemonPostRequest`

URL-safe Base64 encoded encrypted 121-byte Pokémon packet supplied through the `data` query parameter.

| Property | Type | Required | Description |
| -------- | ---- | -------- | ----------- |
| `checksum` | integer | yes | Checksum for the record data. |
| `pid` | integer | yes | The profile ID (PID) of the player that uploaded the Pokémon. |
| `BoxPokemon` | object | yes | The 80-byte box Pokémon data. |
| `dexnum` | integer | yes | The species ID of the Pokémon. |
| `gender` | integer | yes | The gender of the Pokémon (`0x01`==MALE, `0xFE`==FEMALE, `0xFF`==GENDERLESS). |
| `level` | integer | yes | The Pokémon's level. |
| `natDexRequest` | integer | yes | The wanted Pokémon species ID No. |
| `genderRequest` | integer | yes | The wanted Pokémon's gender. |
| `minLevel` | integer | yes | The wanted Pokémon's minimum level. |
| `maxLevel` | integer | yes | The wanted Pokémon's maximum level. |
| `trainerGender` | integer | yes | The trainer's gender. |
| `trainerID` | integer | yes | The trainer's ID No. |
| `secretID` | integer | yes | The trainer's Secret ID No. |
| `OTName` | string | yes | The Pokémon's original trainer. |
| `country` | integer | yes | The trainer's country of origin. |
| `region` | integer | yes | The trainer's region of origin. |
| `trainerClass` | integer | yes | The trainer's class. |
| `isExchanged` | boolean | yes | Has the Pokémon been exchanged? |
| `gameVersion` | integer | yes | The game version the Pokémon was deposited from. |
| `romHackID` | integer | yes | The RomHack ID the Pokémon was deposited from. |
| `romHackVersion` | integer | yes | The RomHack Version the Pokémon was deposited from. |
| `language` | integer | yes | Padding to align the data to the game struct. |