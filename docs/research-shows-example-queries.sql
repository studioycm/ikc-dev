SELECT a.ArenaID,
       COALESCE(d.dogs, 0)             AS dogs,
       COALESCE(j.judges, 0)           AS judges,
       COALESCE(j.breeds_with_dogs, 0) AS breeds_with_dogs,
       COALESCE(r.results, 0)          AS results
FROM (SELECT DISTINCT ArenaID
      FROM `Shows_Dogs_DB`
      WHERE ShowID = 1560
        AND deleted_at IS NULL
      UNION
      SELECT DISTINCT ArenaID
      FROM `Shows_Breeds`
      WHERE ShowID = 1560
      UNION
      SELECT DISTINCT MainArenaID AS ArenaID
      FROM `shows_results`
      WHERE ShowID = 1560) a
         LEFT JOIN (SELECT ArenaID, COUNT(*) AS dogs
                    FROM `Shows_Dogs_DB`
                    WHERE ShowID = 1560
                      AND deleted_at IS NULL
                    GROUP BY ArenaID) d ON d.ArenaID = a.ArenaID
         LEFT JOIN (SELECT sb.ArenaID,
                           COUNT(DISTINCT sb.JudgeID) AS judges,
                           COUNT(DISTINCT sb.RaceID)  AS breeds_with_dogs
                    FROM `Shows_Breeds` sb
                    WHERE sb.ShowID = 1560
                      AND EXISTS (SELECT 1
                                  FROM `Shows_Dogs_DB` sd
                                  WHERE sd.ShowID = sb.ShowID
                                    AND sd.ArenaID = sb.ArenaID
                                    AND sd.BreedID = sb.RaceID
                                    AND sd.deleted_at IS NULL)
                    GROUP BY sb.ArenaID) j ON j.ArenaID = a.ArenaID
         LEFT JOIN (SELECT MainArenaID AS ArenaID, COUNT(*) AS results
                    FROM `shows_results`
                    WHERE ShowID = 1560
                    GROUP BY MainArenaID) r ON r.ArenaID = a.ArenaID
ORDER BY dogs DESC, judges DESC, breeds_with_dogs DESC, results DESC
LIMIT 10;

SELECT a.ArenaID,
       COALESCE(d.dogs, 0)             AS dogs,
       COALESCE(j.judges, 0)           AS judges,
       COALESCE(j.breeds_with_dogs, 0) AS breeds_with_dogs,
       COALESCE(r.results, 0)          AS results
FROM (SELECT DISTINCT ArenaID
      FROM Shows_Dogs_DB
      WHERE ShowID = 1512
        AND deleted_at IS NULL
      UNION
      SELECT DISTINCT ArenaID
      FROM Shows_Breeds
      WHERE ShowID = 1512
      UNION
      SELECT DISTINCT MainArenaID AS ArenaID
      FROM shows_results
      WHERE ShowID = 1512) a
         LEFT JOIN (SELECT ArenaID, COUNT(*) AS dogs
                    FROM Shows_Dogs_DB
                    WHERE ShowID = 1512
                      AND deleted_at IS NULL
                    GROUP BY ArenaID) d ON d.ArenaID = a.ArenaID
         LEFT JOIN (SELECT sb.ArenaID,
                           COUNT(DISTINCT sb.JudgeID) AS judges,
                           COUNT(DISTINCT sb.RaceID)  AS breeds_with_dogs
                    FROM Shows_Breeds sb
                    WHERE sb.ShowID = 1512
                      AND EXISTS (SELECT 1
                                  FROM Shows_Dogs_DB sd
                                  WHERE sd.ShowID = sb.ShowID
                                    AND sd.ArenaID = sb.ArenaID
                                    AND sd.BreedID = sb.RaceID
                                    AND sd.deleted_at IS NULL)
                    GROUP BY sb.ArenaID) j ON j.ArenaID = a.ArenaID
         LEFT JOIN (SELECT MainArenaID AS ArenaID, COUNT(*) AS results
                    FROM shows_results
                    WHERE ShowID = 1512
                    GROUP BY MainArenaID) r ON r.ArenaID = a.ArenaID
ORDER BY dogs DESC, judges DESC, breeds_with_dogs DESC, results DESC
LIMIT 10;
